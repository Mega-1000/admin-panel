<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Orders\StoreOrderMessageRequest;
use App\Http\Requests\Api\Orders\StoreOrderRequest;
use App\Http\Requests\Api\Orders\UpdateOrderDeliveryAndInvoiceAddressesRequest;
use App\Repositories\CustomerRepository;
use App\Repositories\Oldfront\UzytkownicyRepository;
use App\Repositories\OrderAddressRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderMessageAttachmentRepository;
use App\Repositories\OrderMessageRepository;
use App\Repositories\CustomerAddressRepository;
use App\Repositories\OrderPackageRepository;
use App\Repositories\OrderRepository;
use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Repositories\ProductPriceRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\Product;
use App\Entities\Customer;
use App\Entities\CustomerAddress;
use App\Entities\OrderAddress;
use Illuminate\Support\Facades\Hash;

/**
 * Class OrdersController
 * @package App\Http\Controllers\Api
 */
class OrdersController extends Controller
{
    use ApiResponsesTrait;

    /** @var OrderRepository */
    protected $orderRepository;

    /** @var CustomerRepository */
    protected $customerRepository;

    /** @var OrderItemRepository */
    protected $orderItemRepository;

    /** @var ProductRepository */
    protected $productRepository;

    /** @var OrderAddressRepository */
    protected $orderAddressRepository;

    /** @var OrderMessageRepository */
    protected $orderMessageRepository;

    /** @var CustomerAddressRepository */
    protected $customerAddressRepository;

    /** @var ProductPriceRepository */
    protected $productPriceRepository;

    /** @var OrderMessageAttachmentRepository */
    protected $orderMessageAttachmentRepository;

    /** @var OrderPackageRepository */
    protected $orderPackageRepository;

    /** @var UzytkownicyRepository */
    protected $uzytkownicyRepository;

    private $error_code = null;

    private $errors = [
        'missing_products' => 'Musisz dodać przynajmniej jeden produkt do koszyka.',
        'wrong_cart_token' => 'Błędny token zamówienia',
        'missing_customer_login' => 'Musisz podać login',
        'wrong_password' => 'Błędny adres e-mail lub hasło',
        'wrong_phone' => 'Podaj prawidłowy nr telefonu',
        'wrong_product_id' => null
    ];

    private $defaultError = 'Wystąpił wewnętrzny błąd systemu przy składaniu zamówienia. Dział techniczny został o tym poinformowany.';

    /**
     * OrdersController constructor.
     * @param OrderRepository $orderRepository
     * @param CustomerRepository $customerRepository
     * @param OrderItemRepository $orderItemRepository
     * @param ProductRepository $productRepository
     * @param OrderAddressRepository $orderAddressRepository
     * @param OrderMessageRepository $orderMessageRepository
     * @param CustomerAddressRepository $customerAddressRepository
     * @param ProductPriceRepository $productPriceRepository
     * @param OrderMessageAttachmentRepository $orderMessageAttachmentRepository
     * @param OrderPackageRepository $orderPackageRepository
     * @param UzytkownicyRepository $uzytkownicyRepository
     */
    public function __construct(
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        OrderItemRepository $orderItemRepository,
        ProductRepository $productRepository,
        OrderAddressRepository $orderAddressRepository,
        OrderMessageRepository $orderMessageRepository,
        CustomerAddressRepository $customerAddressRepository,
        ProductPriceRepository $productPriceRepository,
        OrderMessageAttachmentRepository $orderMessageAttachmentRepository,
        OrderPackageRepository $orderPackageRepository,
        UzytkownicyRepository $uzytkownicyRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productRepository = $productRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->orderMessageRepository = $orderMessageRepository;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->productPriceRepository = $productPriceRepository;
        $this->orderMessageAttachmentRepository = $orderMessageAttachmentRepository;
        $this->orderPackageRepository = $orderPackageRepository;
        $this->uzytkownicyRepository = $uzytkownicyRepository;
    }

    public function store(StoreOrderRequest $request)
    {
        throw new \Exception("Method deprecated");
    }

    public function newOrder(StoreOrderRequest $request)
    {
        $data = $request->all();
        DB::beginTransaction();
        try {
            $id = $this->newStore($data);
            DB::commit();
            return $this->createdResponse(['order_id' => $id]);
        } catch (\Exception $e) {
            DB::rollBack();
            $message = $this->errors[$this->error_code] ?? $e->getMessage();
            Log::error("Problem with create new order: [{$this->error_code}] $message",
                ['request' => $data, 'class' => get_class($this), 'line' => __LINE__]
            );
            $message = $this->errors[$this->error_code] ?? $this->defaultError;
            return response(json_encode([
                'error_code' => $this->error_code,
                'error_message' => $message
            ]), $this->error_code ? 400 : 500);
        }
    }

    private function newStore($data)
    {
        if (empty($data['order_items']) || !is_array($data['order_items'])) {
            $this->error_code = 'missing_products';
            throw new \Exception();
        }
        $order = null;
        $orderExists = false;
        if (!empty($data['cart_token'])) {
            $order = Order::where('token', $data['cart_token'])->first();
            $orderExists = true;
            if (!$order) {
                $this->error_code = 'wrong_cart_token';
                throw new \Exception();
            }
        } else {
            $order = new Order();
        }

        if ($orderExists) {
            $customer = $order->customer;
        } else {
            $customer = $this->getCustomerByLogin($data['customer_login'] ?? '', $data['phone'] ?? '');
        }

        $order->customer_id = $customer->id;

        if (!$orderExists) {
            $order->status_id = 1;
            $this->assignEmployeeToOrder($order, $customer);
        }

        if (!empty($data['customer_notices'])) {
            $order->customer_notices = $data['customer_notices'];
        }

        if (!empty($data['shipping_abroad'])) {
            $order->shipping_abroad = 1;
        }

        $order->save();

        $this->assignItemsToOrder($order, $data['order_items']);

        $this->updateOrderAddress($order, $data['delivery_address'] ?? [], 'DELIVERY_ADDRESS', $data['phone'] ?? '', 'order');
        $this->updateOrderAddress($order, $data['invoice_address'] ?? [], 'INVOICE_ADDRESS', $data['phone'] ?? '', 'order');
        if (isset($data['is_standard'])) {
            $this->updateOrderAddress($order, $data['delivery_address'] ?? [], 'STANDARD_ADDRESS', $data['phone'] ?? '', 'customer', $data['customer_login'] ?? '');
            $this->updateOrderAddress($order, $data['delivery_address'] ?? [], 'DELIVERY_ADDRESS', $data['phone'] ?? '', 'customer', $data['customer_login'] ?? '');
        }

        return $order->id;
    }

    private function getCustomerByLogin($login, $pass)
    {
        if (empty($login)) {
            $this->error_code = 'missing_customer_login';
            throw new \Exception();
        }
        $customer = Customer::where('login', $login)->first();
        //TODO update old passwords
        if ($customer && !Hash::check($pass, $customer->password) && md5($pass) != $customer->password) {
            $this->error_code = 'wrong_password';
            throw new \Exception();
        }
        if (!$customer) {
            $pass = preg_replace('/[^0-9]/', '', $pass);
            if (strlen($pass) < 9) {
                $this->error_code = 'wrong_phone';
                throw new \Exception();
            }
            $customer = new Customer();
            $customer->login = $login;
            $customer->password = Hash::make($pass);
            $customer->save();
        }
        return $customer;
    }

    private function assignEmployeeToOrder($order, $customer)
    {
        $orderCustomerOpenExists = $this->orderRepository->findWhere(
            [
                ['customer_id', '=', $customer->id],
                ['status_id', '<>', 6],
                ['status_id', '<>', 8],
                ['employee_id', '<>', null],
            ]
        )->first();

        if (!empty($orderCustomerOpenExists)) {
            $order->employee_id = $orderCustomerOpenExists->employee_id;
        }
    }

    private function assignItemsToOrder($order, $items)
    {
        $orderTotal = 0;
        $weight = 0;
        $orderItems = $order->items();
        $order->items()->delete();
        $oldPrices = [];

        foreach ($orderItems as $item) {
            foreach ($this->getPriceColumns() as $column) {
                $oldPrices[$item->product_id][$column] = $item->$column;
            }
        }

        foreach ($items as $item) {
            $product = Product::find($item['id']);
            $price = $product->price;
            if (!$product || !$price) {
                $this->error_code = 'wrong_product_id';
                throw new \Exception();
            }

            $orderItem = new OrderItem();
            $orderItem->quantity = $item['amount'];
            $orderItem->product_id = $product->id;
            foreach ($this->getPriceColumns() as $column) {
                if (!empty($item['old_price']) && isset($oldPrices[$product->id])) {
                    $orderItem->$column = $oldPrices[$product->id][$column];
                } else {
                    $orderItem->$column = $price->$column;
                }
            }
            $orderTotal += $orderItem->net_selling_price_commercial_unit * $orderItem->quantity;

            $order->items()->save($orderItem);

            if (!empty($product->weight_trade_unit)) {
                $weight += $product->weight_trade_unit * $orderItem->quantity;
            }
        }

        $order->total_price = $orderTotal * 1.23;
        $order->weight = $weight;
    }

    private function updateOrderAddress($order, $deliveryAddress, $type, $phone, $relation, $login = '')
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (!is_array($deliveryAddress)) {
            $deliveryAddress = [];
        }

        switch ($relation) {
            case 'order':
                $address = $order->addresses()->where('type', $type)->first();
                $obj = $order;
                if (!$address) {
                    $address = new OrderAddress();
                    $address->phone = $phone;
                    $address->type = $type;
                }
                break;
            case 'customer':
                $address = $order->customer->addresses()->where('type', $type)->first();
                $obj = $order->customer;
                if (!$address) {
                    $address = new CustomerAddress();
                    $address->email = $login;
                    $address->phone = $phone;
                    $address->type = $type;
                }
                break;
            default:
                $this->error_code = 'exception';
                throw new \Exception('Unsupported order address relation');
        }

        foreach ([
            'firstname',
            'lastname',
            'firmname',
            'nip',
            'address',
            'flat_number',
            'city',
            'postal_code'
        ] as $column) {
            if (!empty($deliveryAddress[$column])) {
                $address->$column = $deliveryAddress[$column];
            }
        }


        $obj->addresses()->save($address);
    }

    public function storeMessage(StoreOrderMessageRequest $request)
    {
        try {
            $data = $request->validated();
            $order = $this->orderRepository->findWhere(['id_from_front_db' => $data['front_order_id']])->first();
            $data['order_id'] = $order->id;
            if ($request->status !== 'OPEN') {
                $request->status = 'OPEN';
            }

            //TODO refactor field name
            $data['user_id'] = $data['employee_id'] ?? null;
            unset($data['employee_id']);

            $orderMessage = $this->orderMessageRepository->create($data);
            if ($request->get('files')) {
                foreach ($request->get('files') as $file) {
                    Storage::disk('public')->put("attachments/{$orderMessage->order_id}/{$orderMessage->id}/{$file['attachment_name']}",
                        base64_decode($file['attachment']));
                    $this->orderMessageAttachmentRepository->create([
                        'file' => $file['attachment_name'],
                        'order_message_id' => $orderMessage->id,
                    ]);
                }
            }

            return $this->createdResponse();
        } catch (\Exception $e) {
            Log::error('Problem with store order message.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }

    public function getOrder($frontDbOrderId)
    {
        $order = $this->orderRepository->findWhere(['id_from_front_db' => $frontDbOrderId])->first();
        if (empty($order)) {
            return [];
        }

        $data = $order->toArray();
        $data['status_name'] = $order->status->name;
        if (!empty($order->packages)) {
            $data['packages'] = $order->packages->toArray();
        } else {
            $data['packages'] = null;
        }
        if (!empty($order->employee)) {
            $data['employee'] = $order->employee->toArray();
        } else {
            $data['employee'] = null;
        }

        return $data;
    }

    public function getMessages($frontDbOrderId)
    {
        $order = $this->orderRepository->findWhere(['id_from_front_db' => $frontDbOrderId])->first();

        if (empty($order)) {
            return $this->notFoundResponse("Couldn't find requested Order");
        }

        $messages = $order->messages()->orderBy('created_at')->get();

        $groupedMessages = [];
        if (!empty($messages)) {
            foreach ($messages as $message) {
                $groupedMessages[$message->type][] = $message;
            }
        }

        return $groupedMessages;
    }

    public function getCustomerDeliveryAddress($orderId)
    {
        return $this->orderRepository->find($orderId)->customer->addresses->where('type', '=',
            'DELIVERY_ADDRESS')->first();
    }

    public function getCustomerStandardAddress($orderId)
    {
        return $this->orderRepository->find($orderId)->customer->addresses->where('type', '=',
            'STANDARD_ADDRESS')->first();
    }

    public function getReadyToShipFormAutocompleteData($orderId)
    {
        $order = $this->orderRepository->find($orderId);
        return [
            "DELIVERY_ADDRESS" => $order->addresses->where('type', '=', 'DELIVERY_ADDRESS')->first(),
            "INVOICE_ADDRESS" => $order->addresses->where('type', '=', 'INVOICE_ADDRESS')->first(),
            "shipment_date" => $order->shipment_date,
        ];
    }

    public function updateOrderDeliveryAndInvoiceAddresses(
        UpdateOrderDeliveryAndInvoiceAddressesRequest $request,
        $orderId
    )
    {
        try {
            $order = $this->orderRepository->find($orderId);
            $deliveryAddress = $order->addresses->where('type', '=', 'DELIVERY_ADDRESS')->first();
            $invoiceAddress = $order->addresses->where('type', '=', 'INVOICE_ADDRESS')->first();

            $order->shipment_date = $request->get('shipment_date');
            $order->save();

            $deliveryAddress->update($request->get('DELIVERY_ADDRESS'));
            if (empty($request->get('DELIVERY_ADDRESS')['email'])) {
                $deliveryAddress->update(['email' => $order->customer->login]);
                $deliveryMail = $order->customer->login;
            } else {
                $deliveryMail = $request->get('DELIVERY_ADDRESS')['email'];
            }

            $invoiceAddress->update($request->get('INVOICE_ADDRESS'));

            if ($request->get('remember_delivery_address')) {
                $data = array_merge($request->get('DELIVERY_ADDRESS'), ['type' => 'DELIVERY_ADDRESS']);
                $order->customer->addresses()->updateOrCreate(["type" => "DELIVERY_ADDRESS"], $data);

                try {
                    $dataOldfront = [
                        'dostawa_imie' => $request->get('DELIVERY_ADDRESS')['firstname'],
                        'dostawa_nazwisko' => $request->get('DELIVERY_ADDRESS')['lastname'],
                        'dostawa_telefon' => $request->get('DELIVERY_ADDRESS')['phone'],
                        'dostawa_mail' => $deliveryMail,
                        'dostawa_ulica' => $request->get('DELIVERY_ADDRESS')['address'],
                        'dostawa_ulica_numer' => $request->get('DELIVERY_ADDRESS')['flat_number'],
                        'dostawa_kod_pocztowy' => $request->get('DELIVERY_ADDRESS')['postal_code'],
                        'dostawa_miasto' => $request->get('DELIVERY_ADDRESS')['city'],
                    ];
                    $uzytkownik = $this->uzytkownicyRepository->findByField('login', $order->customer->login)->first();
                    $uzytkownik->update($dataOldfront);
                } catch (\Exception $e) {
                    Log::error('Problem with update customer delivery_adress.',
                        ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
                    );
                    die();
                }
            }

            if ($request->get('remember_invoice_address')) {
                $data = array_merge($request->get('INVOICE_ADDRESS'), ['type' => 'INVOICE_ADDRESS']);
                $order->customer->addresses()->updateOrCreate(["type" => "INVOICE_ADDRESS"], $data);

                try {
                    $dataOldfront = [
                        'faktura_imie' => $request->get('INVOICE_ADDRESS')['firstname'],
                        'faktura_nazwisko' => $request->get('INVOICE_ADDRESS')['lastname'],
                        'faktura_telefon' => $request->get('INVOICE_ADDRESS')['phone'],
                        'faktura_mail' => $request->get('INVOICE_ADDRESS')['email'],
                        'faktura_ulica' => $request->get('INVOICE_ADDRESS')['address'],
                        'faktura_ulica_numer' => $request->get('INVOICE_ADDRESS')['flat_number'],
                        'faktura_kod_pocztowy' => $request->get('INVOICE_ADDRESS')['postal_code'],
                        'faktura_miasto' => $request->get('INVOICE_ADDRESS')['city'],
                        'faktura_nazwa_firmy' => $request->get('INVOICE_ADDRESS')['firmname'],
                        'faktura_nip' => $request->get('INVOICE_ADDRESS')['nip'],
                    ];
                    $uzytkownik = $this->uzytkownicyRepository->findByField('login', $order->customer->login)->first();
                    $uzytkownik->update($dataOldfront);
                } catch (\Exception $e) {
                    Log::error('Problem with update customer invoice_address.',
                        ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
                    );
                    die();
                }
            }

            return $this->okResponse();
        } catch (\Exception $e) {
            Log::error('Problem with update customer invoice and delivery address.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }

    public function orderPackagesCancelled(Request $request, $id)
    {
        try {
            $orderPackage = $this->orderPackageRepository->find($id);

            if (empty($orderPackage)) {
                Log::info('Problem with find orderPackage item with id =' . $id,
                    ['class' => get_class($this), 'line' => __LINE__]
                );
                abort(404);
            }

            if ($request->cancelled == 'true') {
                $orderPackage->status = 'CANCELLED';
                $message = 'Przyjęto anulację paczki.';
            } else {
                $orderPackage->status = 'REJECT_CANCELLED';
                $message = 'Odrzucono anulację paczki.';
            }

            $orderPackage->update();

            return response()->json($message, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('Problem with cancelled packages.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }

    public function getAll(Request $request)
    {
        $orders = $request->user()->orders()
            ->with('status')
            ->with(['items' => function($q) {
                $q->with(['product' => function($w) {
                    $w->with('packing')
                        ->with('price');
                }]);
            }])
            ->with('packages')
            ->with('payments')
            ->with('labels')
            ->with('addresses')
            ->with('invoices')
            ->with('employee')
            ->orderBy('id', 'desc')
            ->get()
        ;

        foreach ($orders as $order) {
            $order->total_sum = $order->getSumOfGrossValues();
            $order->bookedPaymentsSum = $order->bookedPaymentsSum();
        }

        return $orders->toJson();
    }

    public function getByToken(Request $request, $token)
    {
        if (empty($token)) {
            return response("Missing token", 400);
        }
        $order = Order
            ::where('token', $token)
            ->with(['items' => function($q) {
                $q->with(['product' => function ($q) {
                    $q->join('product_prices', 'products.id', '=', 'product_prices.product_id');
                    $q->join('product_packings', 'products.id', '=', 'product_packings.product_id');
                }]);
            }])
            ->first()
        ;
        if (!$order) {
            return response("Order doesn't exist", 400);
        }

        $products = [];

        foreach ($order->items as $item) {
            foreach ($this->getPriceColumns() as $column) {
                $item->product->$column = $item->$column;
            }

            $vat = 1 + $item->product->vat / 100;

            foreach ([
                'selling_price_calculated_unit',
                'selling_price_basic_uni',
                'selling_price_aggregate_unit',
                'selling_price_the_largest_unit'
            ] as $column) {
                $kGross = "gross_$column";
                $kNet = "net_$column";
                $item->product->$kGross = round($item->$kNet * $vat, 2);
            }

            $item->product->gross_price_of_packing = round($item->net_selling_price_commercial_unit * $vat, 2);
            $item->product->amount = $item->quantity;

            $products[] = $item->product;
        }

        return response(json_encode($products));
    }

    private function getPriceColumns()
    {
        return [
            'net_purchase_price_commercial_unit',
            'net_purchase_price_basic_unit',
            'net_purchase_price_calculated_unit',
            'net_purchase_price_aggregate_unit',
            'net_purchase_price_the_largest_unit',
            'net_selling_price_commercial_unit',
            'net_selling_price_basic_unit',
            'net_selling_price_calculated_unit',
            'net_selling_price_aggregate_unit',
            'net_selling_price_the_largest_unit',
            'net_purchase_price_commercial_unit_after_discounts',
            'net_purchase_price_basic_unit_after_discounts',
            'net_purchase_price_calculated_unit_after_discounts',
            'net_purchase_price_aggregate_unit_after_discounts',
            'net_purchase_price_the_largest_unit_after_discounts'
        ];
    }
}
