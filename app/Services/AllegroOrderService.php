<?php namespace App\Services;

use App\Entities\AllegroOrder;
use App\Entities\Order;
use App\Jobs\OrderProformSendMailJob;
use App\Jobs\Orders\CheckDeliveryAddressSendMailJob;
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

    public function findNewOrders($today = true)
    {
        $params = [
	        'offset' => 0,
	        'limit' => '100',
	        'status' => self::READY_FOR_PROCESSING
        ];
        
        if ($today) {
	        $params['updatedAt.gte'] = (new Carbon())->startOfDay()->toIso8601ZuluString();
        }
        
        $url = $this->getRestUrl("/order/checkout-forms?" . http_build_query($params));
        
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
                $deliveryAddressChanged = $this->fixDeliveryAddress($order);
	            $invoiceAddressChanged = $this->fixInvoiceAddress($order);
	            dispatch(new CheckDeliveryAddressSendMailJob($order));
	            
	            if ($deliveryAddressChanged || $invoiceAddressChanged) {
		            dispatch(new OrderProformSendMailJob($order, setting('allegro.address_changed_msg')));
	            }
	
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
            $street = $allegroAddress['street'];

            $firstname = $allegroAddress['firstName'];
            $lastname = $allegroAddress['lastName'];
            $city = $allegroAddress['city'];
            $postal_code = $allegroAddress['zipCode'];
        } else {
            $street = $allegroData['delivery']['pickupPoint']['address']['street'];

            $firstname = 'Paczkomat';
            $lastname = $allegroData['delivery']['pickupPoint']['id'];
            $city = $allegroData['delivery']['pickupPoint']['address']['city'];
            $postal_code = $allegroData['delivery']['pickupPoint']['address']['zipCode'];
        }
        
	    $flat = '';
        
        try {
	        $splittedAddress = AddressSplitter::splitAddress($street);
	        $street = $splittedAddress['streetName'];
	        $flat = $splittedAddress['houseNumber'];
        } catch (\Exception $e) {
        
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
        
        return $address->wasChanged();
    }

    private function fixInvoiceAddress(Order $order): bool
    {
        if (!($allegroData = $this->getOrderDetailsFromApi($order))) {
        	return false;
        }

        return $this->fixInvoiceAddressInvoice($order, $allegroData['invoice']['required']);
    }
	
	private function fixInvoiceAddressInvoice(Order $order, $required): bool {
		$address = $order->getInvoiceAddress();
		if (!($allegroData = $this->getOrderDetailsFromApi($order))) {
			return false;
		}
		
		$allegroAddress = $required
			? $allegroData['invoice']['address']
			: $allegroData['buyer']['address'];
		
		$rawPhone = $allegroAddress['phoneNumber'] ?? $allegroData['buyer']['phoneNumber'] ?? '';
		$phone = preg_replace('/[^0-9]/', '', $rawPhone);
		$phone = substr($phone, -9);
		$street = $allegroAddress['street'];
		$flat = '';
		
		try {
			$splittedAddress = AddressSplitter::splitAddress($street);
			$street = $splittedAddress['streetName'];
			$flat = $splittedAddress['houseNumber'];
		} catch (\Exception $e) {
		
		}
		
		$address->firstname = $required
			? ($allegroAddress['company'] == null ? $allegroAddress['naturalPerson']['firstName'] : null)
			: $allegroData['buyer']['firstName'];
		
		$address->lastname = $required
			? ($allegroAddress['company'] == null ? $allegroAddress['naturalPerson']['lastName'] : null)
			: $allegroData['buyer']['lastName'];
		
		$address->email = $allegroData['buyer']['email'];
		$address->firmname = $required
			? $allegroAddress['company']['name']
			: null;
		$address->nip = $required
			? $allegroAddress['company']['taxId']
			: null;
		$address->address = $street;
		$address->flat_number = $flat;
		$address->city = $allegroAddress['city'];
		$address->postal_code = $required
			? $allegroAddress['zipCode']
			: $allegroAddress['postCode'];
		$address->phone = $phone;
		$address->save();
		
		$order->data_verified_by_allegro_api = true;
		$order->save();
		
		return $address->wasChanged();
	}

    private function findNotValidatedOrdersWithInvalidData()
    {
        $yesterday = (new Carbon())->startOfDay()->subDay(1);
        return Order::where('data_verified_by_allegro_api', '=', false)
            ->whereNotNull('sello_id')
            ->where('created_at', '>=', $yesterday)->get();
    }
}
