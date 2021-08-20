<?php namespace App\Services;

use App\Entities\Allegro_Auth;
use GuzzleHttp\Client;

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
		    /**
		     * TODO maybe here shoud be something other. User flow?
		     */
	    	if (env('APP_ENV') == 'development') {
			    $res = $this->getAuthCodes();
			    /**
			     * after that you need to go to - verification_uri_complete and confirm
			     * then go to here anf check auth
			     */
			    if ($response = $this->checkAuthorizationStatus($res['device_code'])) {
				    $this->authModel = new Allegro_Auth();
				    $this->authModel->id = $this->auth_record_id;
				    $this->authModel->access_token = $response['access_token'];
				    $this->authModel->refresh_token = $response['refresh_token'];
				    $this->authModel->save();
			    }
		    }
		    \Log::error('Brak tokena autoryzującego allegro');
		    return;
	    }
    }

    protected function getAuthCodes()
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
	
	protected function checkAuthorizationStatus(string $deviceId)
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
                $response = $this->request($method, $url, $params, $attachment, false);
            } else {
                return $this->cantGetAlert();
            }
        }
        
        if ($response->getStatusCode() != 200) {
	        return $this->cantGetAlert();
        }
        
        return json_decode((string)$response->getBody(), true);
    }
	
    protected function getAccessToken()
    {
        return $this->authModel->access_token;
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
        return $this->authModel->refresh_token;
    }

    protected function getRestUrl(string $resource): string
    {
        return $this->api_url . $resource;
    }
	
	protected function getAuthUrl(string $resource): string
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
		return false;
	}
}
