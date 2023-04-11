<?php namespace App\Services;

use App\Entities\AllegroOrder;
use App\Entities\Order;
use App\Facades\Mailer;
use App\Mail\AllegroNewOrderEmail;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use VIISON\AddressSplitter\AddressSplitter;
use VIISON\AddressSplitter\Exceptions\SplittingException;


/**
 * Class AllegroOrderService
 *
 * @package App\Services
 *
 * NEW  => nowe">nowe
 * PROCESSING  => w realizacji
 * SUSPENDED  => wstrzymane
 * READY_FOR_SHIPMENT  => do wysłania
 * READY_FOR_PICKUP  => do odbioru
 * SENT  => wysłane
 * PICKED_UP  => odebrane
 * CANCELLED  => anulowane
 */
class AllegroOrderService extends AllegroApiService
{
    const STATUS_NEW = "NEW";
    const STATUS_PROCESSING = "PROCESSING";
    const STATUS_READY_FOR_SHIPMENT = "READY_FOR_SHIPMENT";
    const STATUS_READY_FOR_PICKUP = "READY_FOR_PICKUP";
    const STATUS_SENT = "SENT";
    const STATUS_PICKED_UP = "PICKED_UP";
    const STATUS_CANCELLED = "CANCELLED";
    const STATUS_SUSPENDED = "SUSPENDED";
    const TYPE_BUYER_CANCELLED = "BUYER_CANCELLED ";
    const READY_FOR_PROCESSING = 'READY_FOR_PROCESSING';
    protected $auth_record_id = 2;

    public function __construct()
    {
        parent::__construct();
    }

    public function sendNewOrderMessagesToClients(): void
    {
        $orders = AllegroOrder::where('new_order_message_sent', '=', false)->get();

        foreach ($orders as $order) {
            $this->sendNewOrderMessageToClient($order->order_id);
        }
    }

    public function sendNewOrderMessageToClient(string $orderId): void
    {
        $orderAllegro = AllegroOrder::where('order_id', '=', $orderId)->first();
        $orderAllegro->new_order_message_sent = true;
        $orderAllegro->save();

        Mailer::create()
            ->to($orderAllegro->buyer_email)
            ->send(new AllegroNewOrderEmail());
    }

    public function fixOrdersWithInvalidData()
    {
        $orders = $this->findNotValidatedOrdersWithInvalidData();

        foreach ($orders as $order) {
            try {
                $deliveryAddressChanged = $this->fixDeliveryAddress($order);
                $invoiceAddressChanged = $this->fixInvoiceAddress($order);
            } catch (SplittingException $e) {
                //
            }
        }
    }

    /**
     * @return ?Collection<Order>
     */
    private function findNotValidatedOrdersWithInvalidData(): ?Collection
    {
        $yesterday = (new Carbon())->startOfDay()->subDay();
        /** @var ?Collection<Order> $order */
        $order = Order::where('data_verified_by_allegro_api', '=', false)
            ->whereNotNull('sello_id')
            ->where('created_at', '>=', $yesterday)->get();
        return $order;
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
        } catch (Exception $e) {

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

    private function fixInvoiceAddress(Order $order): bool
    {
        if (!($allegroData = $this->getOrderDetailsFromApi($order))) {
            return false;
        }

        return $this->fixInvoiceAddressInvoice($order, $allegroData['invoice']['required']);
    }

    private function fixInvoiceAddressInvoice(Order $order, $required): bool
    {
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
        } catch (Exception $e) {

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

    /**
     * Funkcja pobiera opłacone zamówienia, których nie ma w systemie
     *
     * @return array
     */
    public function getOrdersOutsideSystem(): array
    {
        $ordersFromOutsideTheSystem = [];
        $offset = $totalCount = 0;

        while ($offset <= $totalCount) {
            $params = [
                'offset' => $offset,
                'limit' => 100,
                'lineItems.boughtAt.lte' => Carbon::now()->addDays('-180')->toISOString(),
            ];
            $url = $this->getRestUrl('/order/checkout-forms?' . http_build_query($params));
            $response = $this->request('GET', $url, $params);
            foreach ($response['checkoutForms'] as $order) {
                if (isset($order['payment']) && $order['payment']['paidAmount'] !== null) {
                    $existingOrders = Order::where('allegro_form_id', 'like', '%' . $order['id'] . '%')
                        ->orWhere('allegro_payment_id', 'like', '%' . $order['payment']['id'] . '%')->get();
                    if ($existingOrders->count() === 0) {
                        $ordersFromOutsideTheSystem[] = $order;
                    } else {
                        $existingOrder = $existingOrders->first();

                        if (empty($existingOrder->allegro_operation_date)) {
                            $existingOrder->allegro_operation_date = $order['updatedAt'];
                        }

                        $existingOrder->customer->nick_allegro = $order['buyer']['login'];
                        $existingOrder->preferred_invoice_date = $order['payment']['finishedAt'];
                        $existingOrder->allegro_form_id = $order['id'];
                        $existingOrder->customer->save();
                        $existingOrder->save();
                    }
                }
            }
            $totalCount = $response['totalCount'];
            $offset += 100;
        }

        return $ordersFromOutsideTheSystem;
    }

    /**
     * @param array $addParams
     *
     * @return array
     */
    public function getPendingOrders($addParams = []): array
    {
        $params = [
            'offset' => 0,
            'limit' => 100,
            'status' => self::READY_FOR_PROCESSING,
            'fulfillment.status' => 'NEW'
        ];
        $params = $params + $addParams;
        $url = $this->getRestUrl('/order/checkout-forms?' . http_build_query($params));
        $response = $this->request('GET', $url, $params);

        return $response && is_array($response) && array_key_exists('checkoutForms', $response) ? $response['checkoutForms'] : [];
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getAllOrders(int $limit, int $offset): array
    {
        $params = [
            'offset' => $offset,
            'limit' => $limit,
        ];
        $url = $this->getRestUrl('/order/checkout-forms?' . http_build_query($params));
        return $this->request('GET', $url, $params) ?? [];
    }

    /**
     * @return array
     */
    public function getBuyerCancelled(): array
    {
        $params = [
            'type' => self::TYPE_BUYER_CANCELLED,
        ];
        $url = $this->getRestUrl('/order/events?' . http_build_query($params));

        $response = $this->request('GET', $url, []);

        return $response && is_array($response) && array_key_exists('events', $response) ? $response['events'] : [];
    }

    /**
     * @return array
     */
    public function getCustomerReturns(): array
    {
        $returns = [];
        $offset = 0;
        do {
            $params = [
                'offset' => $offset,
                'createdAt.gte' => Carbon::now()->addDays('-200')->toISOString(),
            ];
            $url = $this->getRestUrl('/order/customer-returns?' . http_build_query($params));
            $response = $this->request('GET', $url, $params);
            $totalCount = ($response && is_array($response)) ? $response['count'] : 0;
            $returns = array_merge($response && is_array($response) && array_key_exists('customerReturns', $response) ? $response['customerReturns'] : [], $returns);
            $offset += 100;
        } while ($offset < $totalCount);
        return $returns;
    }

    /**
     * @return array
     */
    public function getPaymentsRefunds(): array
    {
        $refunds = [];
        $offset = 0;
        do {
            $params = [
                'offset' => $offset,
                'occurredAt.gte' => Carbon::now()->addDays('-200')->toISOString(),
                'status' => 'SUCCESS',
            ];
            $url = $this->getRestUrl('/payments/refunds?' . http_build_query($params));
            $response = $this->request('GET', $url, $params);
            $totalCount = ($response && is_array($response)) ? $response['totalCount'] : 0;
            if ($response && is_array($response) && array_key_exists('refunds', $response)) {
                $refunds = array_merge($refunds, $response['refunds']);
            }
            $offset += 50;
        } while ($offset < $totalCount);
        return $refunds;
    }

    /**
     * @return array|bool
     */
    public function setSellerOrderStatus($formId, $status)
    {
        $params = [
            "status" => $status
        ];
        $url = $this->getRestUrl("/order/checkout-forms/{$formId}/fulfillment");
        $response = $this->request('PUT', $url, $params);

        return $response;
    }
}
