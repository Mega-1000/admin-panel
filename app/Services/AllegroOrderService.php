<?php namespace App\Services;

use App\Entities\AllegroOrder;
use App\Entities\Order;
use App\Mail\AllegroNewOrderEmail;
use Carbon\Carbon;
use VIISON\AddressSplitter\AddressSplitter;
use VIISON\AddressSplitter\Exceptions\SplittingException;


/**
 * Class AllegroOrderService
 * @package App\Services
 *
 */
class AllegroOrderService extends AllegroApiService
{
    protected $auth_record_id = 2;
    const READY_FOR_PROCESSING = 'READY_FOR_PROCESSING';

    public function __construct()
    {
        parent::__construct();
    }

    public function findNewOrders()
    {
        $today = urlencode((new Carbon())->startOfDay()->toIso8601ZuluString());
        $url = $this->getRestUrl(
            "/order/checkout-forms?offset=0&limit=100" .
            "&updatedAt.gte=" . $today .
            "&status=" . self::READY_FOR_PROCESSING
        );
        if (!($orders = $this->request('GET', $url, []))) {
        	return;
        }
	    
        foreach ($orders['checkoutForms'] as $order) {
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
        return $this->request('GET', $url, []);
    }

    private function fixDeliveryAddress(Order $order): bool
    {
        $address = $order->deliveryAddress;

        if (!($allegroData = $this->getOrderDetailsFromApi($order))) {
        	return false;
        }
        $allegroAddress = $allegroData['delivery']['address'];
        $phone = preg_replace('/[^0-9]/', '', $allegroAddress['phoneNumber']);
        $phone = substr($phone, -9);

        if ($allegroData['delivery']['pickupPoint'] == null) {
            $street = AddressSplitter::splitAddress($allegroAddress['street'])['streetName'];
            $flat = AddressSplitter::splitAddress($allegroAddress['street'])['houseNumber'];

            $firstname = $allegroAddress['firstName'];
            $lastname = $allegroAddress['lastName'];
            $city = $allegroAddress['city'];
            $postal_code = $allegroAddress['zipCode'];
        } else {
            $street = AddressSplitter::splitAddress($allegroData['delivery']['pickupPoint']['address']['street'])['streetName'];
            $flat = AddressSplitter::splitAddress($allegroData['delivery']['pickupPoint']['address']['street'])['houseNumber'];

            $firstname = 'Paczkomat';
            $lastname = $allegroData['delivery']['pickupPoint']['id'];
            $city = $allegroData['delivery']['pickupPoint']['address']['city'];
            $postal_code = $allegroData['delivery']['pickupPoint']['address']['zipCode'];
        }
	
	    $address->firstname = $firstname;
	    $address->lastname = $lastname;
	    $address->address = $street;
	    $address->flat_number = $flat;
	    $address->city = $city;
	    $address->postal_code = $postal_code;
	    
        $address->firmname = $allegroAddress['companyName'];
        $address->email = $allegroData['buyer']['email'];
        $address->phone = $phone;
        $address->save();

        $order->data_verified_by_allegro_api = true;
        $order->save();
        
        return true;
    }

    private function fixInvoiceAddress(Order $order): bool
    {
        if (!($allegroData = $this->getOrderDetailsFromApi($order))) {
        	return false;
        }

        if ($allegroData['invoice']['required'] == false) {
            return $this->fixInvoiceAddressInvoiceNotRequired($order);
        } else {
            return $this->fixInvoiceAddressInvoiceRequired($order);
        }
    }

    private function fixInvoiceAddressInvoiceRequired(Order $order)
    {
        $address = $order->getInvoiceAddress();
        if (!($allegroData = $this->getOrderDetailsFromApi($order))) {
        	return false;
        }

        $allegroAddress = $allegroData['invoice']['address'];
        $rawPhone = $allegroAddress['phoneNumber'] ?? $allegroData['buyer']['phoneNumber'] ?? '';
        $phone = preg_replace('/[^0-9]/', '', $rawPhone);
        $phone = substr($phone, -9);
        $street = AddressSplitter::splitAddress($allegroAddress['street'])['streetName'];
        $flat = AddressSplitter::splitAddress($allegroAddress['street'])['houseNumber'];

        $address->firstname = $allegroAddress['company'] == null ? $allegroAddress['naturalPerson']['firstName'] : null;
        $address->lastname = $allegroAddress['company'] == null ? $allegroAddress['naturalPerson']['lastName'] : null;
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
        
        return true;
    }

    private function fixInvoiceAddressInvoiceNotRequired(Order $order)
    {
        $address = $order->getInvoiceAddress();
	    if (!($allegroData = $this->getOrderDetailsFromApi($order))) {
		    return false;
	    }

        $allegroAddress = $allegroData['buyer']['address'];
        $rawPhone = $allegroAddress['phoneNumber'] ?? $allegroData['buyer']['phoneNumber'] ?? '';
        $phone = preg_replace('/[^0-9]/', '', $rawPhone);
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
        
        return true;
    }

    private function findNotValidatedOrdersWithInvalidData()
    {
        $yesterday = (new Carbon())->startOfDay()->subDay(1);
        return Order::where('data_verified_by_allegro_api', '=', false)
            ->whereNotNull('sello_id')
            ->where('created_at', '>=', $yesterday)->get();
    }
}
