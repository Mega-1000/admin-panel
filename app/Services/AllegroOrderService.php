<?php namespace App\Services;

use App\Entities\Allegro_Auth;
use App\Entities\AllegroOrder;
use App\Entities\Label;
use App\Entities\Order;
use App\Entities\SelTransaction;
use App\Facades\Mailer;
use App\Mail\AllegroNewOrderEmail;
use App\Mail\TestMail;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use VIISON\AddressSplitter\AddressSplitter;
use VIISON\AddressSplitter\Exceptions\SplittingException;


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
    const INVALID_DATA_LABEL = 184;

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

    public function findNewOrders()
    {
        $today = urlencode((new Carbon())->startOfDay()->toIso8601ZuluString());
        $url = $this->getRestUrl(
            "/order/checkout-forms?offset=0&limit=100" .
            "&updatedAt.gte=" . $today .
            "&status=" . self::READY_FOR_PROCESSING
        );
        $orders = json_decode((string)$this->request('GET', $url, [])->getBody(), true)['checkoutForms'];
        foreach ($orders as $order) {
            $orderModel = AllegroOrder::firstOrNew(['order_id' => $order['id']]);
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

        \Mailer::create()
            ->to($orderModel->buyer_email)
            ->send(new AllegroNewOrderEmail());
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

    public function fixOrdersWithInvalidData()
    {
        $orders = $this->findNotValidatedOrdersWithInvalidData();

        foreach ($orders as $order) {
            try {
                $this->fixDeliveryAddress($order);
                $this->fixInvoiceAddress($order);
            } catch (SplittingException $e) {
                //
            }
        }
    }

    public function getOrderDetailsFromApi(Order $order)
    {
        if (!$order->sello_id) {
            return false;
        }
        $id = $order->selloTransaction->tr_CheckoutFormId;
        $url = $this->getRestUrl(
            "/order/checkout-forms/{$id}"
        );
        return json_decode((string)$this->request('GET', $url, [])->getBody(), true);
    }

    private function fixDeliveryAddress(Order $order): void
    {
        $address = $order->deliveryAddress;

        if ($address->firstname == 'Paczkomat') {
            return;
        }

        $allegroData = $this->getOrderDetailsFromApi($order);
        $allegroAddress = $allegroData['delivery']['address'];
        $phone = preg_replace('/[^0-9]/', '', $allegroAddress['phoneNumber']);
        $phone = substr($phone, -9);
        $street = AddressSplitter::splitAddress($allegroAddress['street'])['streetName'];
        $flat = AddressSplitter::splitAddress($allegroAddress['street'])['houseNumber'];

        $address->firstname = $allegroAddress['firstName'];
        $address->lastname = $allegroAddress['lastName'];
        $address->email = $allegroData['buyer']['email'];
        $address->firmname = $allegroAddress['companyName'];
        $address->address = $street;
        $address->flat_number = $flat;
        $address->city = $allegroAddress['city'];
        $address->postal_code = $allegroAddress['zipCode'];
        $address->phone = $phone;
        $address->save();

        $order->data_verified_by_allegro_api = true;
        $order->save();
    }

    private function fixInvoiceAddress(Order $order): void
    {
        $address = $order->invoiceAddress;
        $allegroData = $this->getOrderDetailsFromApi($order);

        if ($allegroData['invoice']['required'] == false) {
            $this->fixInvoiceAddressInvoiceNotRequired($order);
        } else {
            $this->fixInvoiceAddressInvoiceRequired($order);
        }
    }

    private function fixInvoiceAddressInvoiceRequired(Order $order)
    {
        $address = $order->invoiceAddress;
        $allegroData = $this->getOrderDetailsFromApi($order);

        $allegroAddress = $allegroData['invoice']['address'];
        $phone = preg_replace('/[^0-9]/', '', $allegroAddress['phoneNumber']);
        $phone = substr($phone, -9);
        $street = AddressSplitter::splitAddress($allegroAddress['street'])['streetName'];
        $flat = AddressSplitter::splitAddress($allegroAddress['street'])['houseNumber'];

        $address->firstname = null;
        $address->lastname = null;
        $address->email = $allegroData['buyer']['email'];
        $address->firmname = $allegroAddress['company']['name'];
        $address->nip = $allegroAddress['company']['taxId'];
        $address->address = $street;
        $address->flat_number = $flat;
        $address->city = $allegroAddress['city'];
        $address->postal_code = $allegroAddress['zipCode'];
        $address->phone = $phone;
        $address->save();

        $order->data_verified_by_allegro_api = true;
        $order->save();
    }

    private function fixInvoiceAddressInvoiceNotRequired(Order $order)
    {
        $address = $order->invoiceAddress;
        $allegroData = $this->getOrderDetailsFromApi($order);

        $allegroAddress = $allegroData['buyer']['address'];
        $phone = preg_replace('/[^0-9]/', '', $allegroData['buyer']['phoneNumber']);
        $phone = substr($phone, -9);
        $street = AddressSplitter::splitAddress($allegroAddress['street'])['streetName'];
        $flat = AddressSplitter::splitAddress($allegroAddress['street'])['houseNumber'];

        $address->firstname = $allegroData['buyer']['firstName'];
        $address->lastname = $allegroData['buyer']['lastName'];
        $address->email = $allegroData['buyer']['email'];
        $address->firmname = null;
        $address->nip = null;
        $address->address = $street;
        $address->flat_number = $flat;
        $address->city = $allegroAddress['city'];
        $address->postal_code = $allegroAddress['postCode'];
        $address->phone = $phone;
        $address->save();

        $order->data_verified_by_allegro_api = true;
        $order->save();
    }

    private function findNotValidatedOrdersWithInvalidData()
    {
        $yesterday = (new Carbon())->startOfDay();//->subDay(1); @TODO do testow
        return Order::where('data_verified_by_allegro_api', '=', false)
            ->where('sello_id', '!=', null)
            ->where('created_at', '>=', $yesterday)->get();
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
