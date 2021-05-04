<?php namespace App\Services;

use App\Entities\Allegro_Auth;
use App\Entities\AllegroOrder;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;


/**
 * Class AllegroOrderService
 * @package App\Services
 *
 * @TODO this class have many methods that are also present in AllegroDisputeService ->
 * this methods should be decoupled in to AllegroApiService
 */
class AllegroOrderService
{

    const AUTH_RECORD_ID = 2;
    const READY_FOR_PROCESSING = 'READY_FOR_PROCESSING';

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
        $this->client = new Client();
        $this->authModel = Allegro_Auth::find(self::AUTH_RECORD_ID);
    }

    public function findNewOrders(): void
    {
        $url = $this->getRestUrl("/order/checkout-forms?offset=0&limit=100&sort=-updatedAt&status=" .
            self::READY_FOR_PROCESSING);
        $orders = json_decode((string)$this->request('GET', $url, [])->getBody(), true)['checkoutForms'];

        foreach ($orders as $order) {
            $orderModel = new AllegroOrder();
            $orderModel->order_id = $order['id'];
            $orderModel->buyer_email = $order['buyer']['email'];
            $orderModel->save();
        }
    }

    public function sendNewOrderMessagesToClients(): void
    {
        $orders = AllegroOrder::where('new_order_message_sent', '=', false)->get();

        foreach ($orders as $order) {
            $this->sendNewOrderMessageToClient($order->order_id);
        }
    }

    public function sendNewOrderMessageToClient($orderId): void
    {
        $orderModel = AllegroOrder::where('order_id', '=', $orderId)->first();
        $orderModel->new_order_message_sent = true;
        $orderModel->save();

        Mail::raw(setting('site.new_allegro_order_msg'), function ($message) use ($orderModel) {
            $message
                ->to($orderModel->buyer_email)
                ->subject('Prosimy nie odpowiadać na tę wiadomość.');
        });
    }

    public function getAuthCodes()
    {
        $response = $this->client->post(env('ALLEGRO_AUTH_CODES_URL'), [
            'headers' => [
                'Authorization' => $this->getBasicAuthString(),
                'Content-type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'client_id' => env('ALLEGRO_CLIENT_ID')
            ]
        ]);
        return json_decode((string)$response->getBody(), true);
    }

    public function checkAuthorizationStatus(string $deviceId)
    {
        $url = env('ALLEGRO_AUTH_STATUS_URL') . $deviceId;
        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => $this->getBasicAuthString(),
            ]
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    private function refreshTokens()
    {
        $url = env('ALLEGRO_REFRESH_URL') . $this->getRefreshToken();
        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => $this->getBasicAuthString()
            ]
        ]);
        $response = json_decode((string)$response->getBody(), true);
        $this->authModel->access_token = $response['access_token'];
        $this->authModel->refresh_token = $response['refresh_token'];
        $this->authModel->save();
    }


    private function request(string $method, string $url, array $params)
    {
        $headers = [
            'Authorization' => "Bearer " . $this->getAccessToken(),
            'Content-Type' => 'application/vnd.allegro.public.v1+json'
        ];

        try {
            $response = $this->client->request(
                $method,
                $url,
                [
                    'headers' => $headers,
                    'json' => $params
                ]
            );
        } catch (\Exception $e) {
            if ($e->getCode() == 401) {
                $this->refreshTokens();
                $response = $this->request($method, $url, $params);
            } else {
                return $this->cantCallApiAlert();
            }
        }
        return $response;
    }

    private function getAccessToken()
    {
        return $this->authModel->access_token;
    }

    private function getRefreshToken()
    {
        return $this->authModel->refresh_token;
    }

    private function getRestUrl(string $resource): string
    {
        return env('ALLEGRO_REST_URL') . $resource;
    }

    private function getBasicAuthString(): string
    {
        return 'Basic ' . base64_encode(env('ALLEGRO_CLIENT_ID') . ':' . env('ALLEGRO_CLIENT_SECRET'));
    }

    private function cantCallApiAlert(): bool
    {
        // what should we do in this case?
        return false;
    }

}
