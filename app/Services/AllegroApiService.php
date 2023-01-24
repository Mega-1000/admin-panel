<?php namespace App\Services;

use App\Entities\Allegro_Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AllegroApiService
{
	protected $auth_record_id = null;
	protected $sandbox = false;
	protected $api_url = '';
	protected $auth_url = '';

	/**
	 * @var Client
	 */
	private $client;

	/**
	 * @var Allegro_Auth
	 */
	private $authModel;

	public function __construct()
	{
		$this->sandbox = env('ALLEGRO_SANDBOX', false);

		$this->api_url = !$this->sandbox
			? 'https://api.allegro.pl'
			: 'https://api.allegro.pl.allegrosandbox.pl';
		$this->auth_url = !$this->sandbox
			? 'https://allegro.pl/auth/oauth'
			: 'https://allegro.pl.allegrosandbox.pl/auth/oauth';

		$this->authModel = Allegro_Auth::find($this->auth_record_id);

		$this->client = new Client();

		if (empty($this->authModel)) {
			\Log::error('Brak tokena autoryzującego allegro');
		}
	}

	public function getAuthCodes()
	{
		$response = $this->client->post(
			$this->getAuthUrl('/device'), [
			'headers' => [
				'Authorization' => $this->getBasicAuthString(),
				'Content-type' => 'application/x-www-form-urlencoded'
			],
			'form_params' => [
				'client_id' => env('ALLEGRO_CLIENT_ID')
			]
		]);

		if ($response->getStatusCode() != 200) {
			return false;
		}

		$response = json_decode((string) $response->getBody(), true);
		\Log::info('Allegro device auth info', $response);
		return $response;
	}

	protected function authApplication() {
		try {
			$response = $this->client->post(
				$this->getAuthUrl('/token?grant_type=client_credentials'), [
				'headers' => [
					'Authorization' => $this->getBasicAuthString(),
					'Content-type' => 'application/x-www-form-urlencoded'
				]
			]);
		} catch (\Exception $e) {
			return false;
		}

		return json_decode((string)$response->getBody(), true);
	}

	public function authToken($authorization_code) {
		try {
			$response = $this->client->post(
				$this->getAuthUrl('/token?grant_type=authorization_code'), [
				'headers' => [
					'Authorization' => $this->getBasicAuthString(),
					'Content-type' => 'application/x-www-form-urlencoded'
				],
				'form_params' => [
					'code' => $authorization_code,
					'redirect_uri' => url()->current(),
				]
			]);
		} catch (\Exception $e) {
			return false;
		}

		return json_decode((string)$response->getBody(), true);
	}

	public function checkAuthorizationStatus(string $deviceId)
	{
		$url = $this->getAuthUrl('/token?grant_type=urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Adevice_code&device_code=') . $deviceId;
		$response = $this->client->post($url, [
			'headers' => [
				'Authorization' => $this->getBasicAuthString(),
			]
		]);
		if ($response->getStatusCode() != 200) {
			return false;
		}
		return json_decode((string)$response->getBody(), true);
	}

	protected function refreshTokens()
	{
		$url = $this->getAuthUrl('/token?grant_type=refresh_token&refresh_token=') . $this->getRefreshToken();

		if (!($response = $this->client->post($url, [
			'headers' => [
				'Authorization' => $this->getBasicAuthString()
			]
		]))) {
			return false;
		}
		$response = json_decode((string)$response->getBody(), true);

		$this->authModel->access_token = $response['access_token'];
		$this->authModel->refresh_token = $response['refresh_token'];
		$this->authModel->save();
	}

	protected function request(string $method, string $url, array $params, $attachment = null, $first = true)
	{
		if (!$this->getAccessToken()) {
			Log::error('AllegroApiService: acces token not found');
			return false;
		}

		$headers = [
			// 'Accept' => 'application/vnd.allegro.public.v1+json',
			'Authorization' => "Bearer " . $this->getAccessToken(),
			'Content-Type' => 'application/vnd.allegro.public.v1+json'
		];

		try {
			$data =
				[
					'headers' => $headers,
					'json' => $params
				];
			// save to link for files
			if( isset($params['sink']) ) {
				$data['sink'] = $params['sink'];
			}
			if($attachment){
				$data['multipart'] = [$attachment];
			}
			$response = $this->client->request(
				$method,
				$url,
				$data
			);

		} catch (\Exception $e) {
			if ($e->getCode() == 401 && $first) {
				if ($this->getRefreshToken()) {
					$this->refreshTokens();
				} else {
					$this->fetchAccessToken();
				}
				return $response = $this->request($method, $url, $params, $attachment, false);
			} else {
				Log::error('AllegroApiService: request: ' . $e->getMessage());
				return $this->cantGetAlert();
			}
		}
		if ($response->getStatusCode() != 200) {
		    if ($response->getStatusCode() != 204) {
                return $this->cantGetAlert();
            }
		    return true;
		}
		if( isset($params['sink']) ) {
			return $response;
		}

		return json_decode((string)$response->getBody(), true);
	}

	protected function getAccessToken()
	{
		return $this->authModel ? $this->authModel->access_token : false;
	}

	protected function fetchAccessToken()
	{
		try {
			if (!($response = $this->authApplication())) {
				return false;
			}

			$this->authModel = $this->authModel ?? new Allegro_Auth();
			$this->authModel->id = $this->auth_record_id;
			$this->authModel->access_token = $response['access_token'];
			$this->authModel->refresh_token = isset($response['refresh_token']) ? $response['refresh_token'] : '';
			$this->authModel->save();
		} catch (\Exception $e) {
			return false;
		}
		return true;
	}

	protected function getRefreshToken()
	{
		return $this->authModel ? $this->authModel->refresh_token : false;
	}

	public function getRestUrl(string $resource): string
	{
		return $this->api_url . $resource;
	}

	public function getAuthUrl(string $resource): string
	{
		return $this->auth_url . $resource;
	}

	protected function getBasicAuthString(): string
	{
		return 'Basic ' . base64_encode(env('ALLEGRO_CLIENT_ID') . ':' . env('ALLEGRO_CLIENT_SECRET'));
	}

	private function cantGetAlert(): bool
	{
		// what should we do in this case?
		Log::error('AllegroApiService: request error.');
		return false;
	}
}
