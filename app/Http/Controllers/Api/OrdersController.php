<?php

namespace App\Http\Controllers\Api;


use App\Domains\DelivererPackageImport\Exceptions\OrderNotFoundException;
use App\Entities\Country;
use App\Entities\Customer;
use App\Entities\EmailSetting;
use App\Entities\FirmSource;
use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderAddress;
use App\Entities\OrderDates;
use App\Entities\OrderPackage;
use App\Entities\Status;
use App\Entities\WorkingEvents;
use App\Enums\PackageStatus;
use App\Facades\Mailer;
use App\Helpers\BackPackPackageDivider;
use App\Helpers\ChatHelper;
use App\Helpers\GetCustomerForAdminEdit;
use App\Helpers\GetCustomerForNewOrder;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPackagesCalculator;
use App\Helpers\OrderPriceCalculator;
use App\Helpers\TransportSumCalculator;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OrdersController as OrdersControllerApp;
use App\Http\Requests\Api\Orders\AcceptReceivingOrderRequest;
use App\Http\Requests\Api\Orders\DeclineProformRequest;
use App\Http\Requests\Api\Orders\StoreOrderMessageRequest;
use App\Http\Requests\Api\Orders\StoreOrderRequest;
use App\Http\Requests\Api\Orders\UpdateOrderDeliveryAndInvoiceAddressesRequest;
use App\Http\Requests\ScheduleOrderReminderRequest;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\OrderStatusChangedNotificationJob;
use App\Jobs\SendReminderAboutOfferJob;
use App\Mail\SendOfferToCustomerMail;
use App\Repositories\CustomerAddressRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderAddressRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderMessageAttachmentRepository;
use App\Repositories\OrderMessageRepository;
use App\Repositories\OrderPackageRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductPackingRepository;
use App\Repositories\ProductPriceRepository;
use App\Repositories\ProductRepository;
use App\Services\EmailSendingService;
use App\Services\Label\AddLabelService;
use App\Services\OrderAddressesService;
use App\Services\OrderPackageService;
use App\Services\ProductService;
use App\Services\WorkingEventsService;
use Carbon\Carbon;
use Exception;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Class OrdersController
 *
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

    private $error_code = null;

    private $errors = [
        'missing_products' => 'Musisz dodać przynajmniej jeden produkt do koszyka.',
        'wrong_cart_token' => 'Błędny token zamówienia',
        'missing_customer_login' => 'Musisz podać login',
        'wrong_password' => 'Błędny adres e-mail lub hasło',
        'wrong_phone' => 'Podaj prawidłowy nr telefonu',
        'package_must_be_cancelled' => 'Paczka musi pierw zostać zanulowana',
        'product_not_found' => 'Nie znaleziono produktu. Sprawdź czy nie został usunięty',
        'wrong_product_id' => null
    ];

    private $defaultError = 'Wystąpił wewnętrzny błąd systemu przy składaniu zamówienia. Dział techniczny został o tym poinformowany.';

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var ProductPackingRepository
     */
    private $productPackingRepository;

    /**
     * OrdersController constructor.
     *
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
     * @param ProductService $productService
     * @param ProductPackingRepository $productPackingRepository
     */
    public function __construct(
        OrderRepository                  $orderRepository,
        CustomerRepository               $customerRepository,
        OrderItemRepository              $orderItemRepository,
        ProductRepository                $productRepository,
        OrderAddressRepository           $orderAddressRepository,
        OrderMessageRepository           $orderMessageRepository,
        CustomerAddressRepository        $customerAddressRepository,
        ProductPriceRepository           $productPriceRepository,
        OrderMessageAttachmentRepository $orderMessageAttachmentRepository,
        OrderPackageRepository           $orderPackageRepository,
        ProductService                   $productService,
        ProductPackingRepository         $productPackingRepository
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
        $this->productService = $productService;
        $this->productPackingRepository = $productPackingRepository;
    }

    /**
     * @throws Exception
     */
    public function store(StoreOrderRequest $request)
    {
        throw new Exception("Method deprecated");
    }

    /**
     * Create or update order
     *
     * @param StoreOrderRequest $request
     * @param ProductService $productService
     * @param OrderPackagesCalculator $orderPackagesCalculator
     * @return JsonResponse
     */
    public function newOrder(StoreOrderRequest $request, ProductService $productService, OrderPackagesCalculator $orderPackagesCalculator): JsonResponse
    {
        $data = $request->all();

        $customer = Customer::where('login', $data['customer_login'])->first();
        $customer = $customer ?? auth()->guard('api')->user();

        if ($customer === null && array_key_exists('customer_login', $data)) {
            if (!array_key_exists('phone', $data)) {
                throw new NotFoundException('Phone number is not existing, need this information to create new (not existing) customer', 500);
            }
            // ensure to get last 9 number from data['phone']
            if (strlen($data['phone']) > 9) {
                $data['phone'] = substr($data['phone'], -9);
            }
            if (!array_key_exists('customer_login', $data) || ($data['customer_login'] ?? '') === '') {
                throw new NotFoundException('No customer login in request data', 500);
            }
            $customer = Customer::query()->create([
                'login' => $data['customer_login'],
                'status' => 'ACTIVE',
                'password' => Hash::make($data['phone']),
            ]);
        }

        try {
            DB::beginTransaction();

            $orderBuilder = (new OrderBuilder())
                ->setPackageGenerator(new BackPackPackageDivider())
                ->setPriceCalculator(new OrderPriceCalculator())
                ->setProductService($productService);

            if (empty($data['cart_token'])) {
                $orderBuilder
                    ->setTotalTransportSumCalculator(new TransportSumCalculator())
                    ->setUserSelector(new GetCustomerForNewOrder());
            } else {
                $orderBuilder->setUserSelector(new GetCustomerForAdminEdit());
            }

            $builderData = $orderBuilder->newStore($data, $customer);

            DB::commit();

            $order = Order::find($builderData['id']);
            $order->firm_source_id = FirmSource::byFirmAndSource(config('orders.firm_id'), 2)->value('id');
            $order->packages_values = json_encode($data['packages']  ?? null);
            $order->save();

            $orderAddresses = $order->addresses()->get();

            if (empty($data['cart_token'])) {
                foreach ($orderAddresses as $orderAddress) {
                    OrderAddressesService::updateOrderAddressFromCustomer($orderAddress, $customer);
                }
            }

            $builderData['token'] = $order->getToken();

            $order->updateQuietly(['packages_values' => $orderPackagesCalculator->calculate($order)]);

            $fullCost = OrderPackagesCalculator::getFullCost($order);

            $order->updateQuietly(['shipment_price_for_client_automatic' => $fullCost]);
            $order->updateQuietly(['shipment_price_for_client' => $fullCost]);

            if ($request->get('hide_from_customer')) {
                $order->update([
                    'is_hidden' => $request->get('hide_from_customer'),
                    'status_id' => 1,
                ]);
            } else {
                $order->update(['status_id' => 3]);
            }

            dispatch_now(new OrderStatusChangedNotificationJob($order->id));

            $order->orderOffer()->firstOrNew([
                'order_id' => $order->id,
                'message' => Status::find(18)->message,
            ]);

            if ($order->created_at->format('Y-m-d H:i:s') === $order->updated_at->format('Y-m-d H:i:s')) {
                dispatch(new DispatchLabelEventByNameJob($order, "new-order-created"));
            }

            return response()->json($builderData + [
                'newAccount' => $customer->created_at->format('Y-m-d H:i:s') === $customer->updated_at->format('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            $this->error_code = $e->getMessage();

            $message = $this->errors[$this->error_code] ?? $e->getMessage();

            Log::error(
                "Problem with creating a new order: [{$this->error_code}] $message" . '. Trace log: ' . $e->getTraceAsString(),
                ['request' => $data, 'class' => $e->getFile(), 'line' => $e->getLine()]
            );

            $message = $this->errors[$this->error_code] ?? $this->defaultError;

            return response()->json([
                'error_code' => $this->error_code,
                'error_message' => $message
            ], $this->error_code ? 400 : 500);
        }
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
                    Storage::disk('public')->put(
                        "attachments/{$orderMessage->order_id}/{$orderMessage->id}/{$file['attachment_name']}",
                        base64_decode($file['attachment'])
                    );
                    $this->orderMessageAttachmentRepository->create([
                        'file' => $file['attachment_name'],
                        'order_message_id' => $orderMessage->id,
                    ]);
                }
            }

            return $this->createdResponse();
        } catch (Exception $e) {
            $message = $e->getMessage();
            Log::error("Problem with store order message: $message", ['class' => $e->getFile(), 'line' => $e->getLine()]);
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
        $order = Order::where('id_from_front_db', $frontDbOrderId)->first();

        if ($order === null) {
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

    /**
     * Get customer delivery address
     *
     * @param int $orderId
     * @return JsonResponse
     */
    public function getCustomerDeliveryAddress(int $orderId)
    {
        $customerDeliveryAddress = $this->orderRepository->find($orderId)->customer->addresses->where(
            'type',
            '=',
            'DELIVERY_ADDRESS'
        )->first();
        if ($customerDeliveryAddress === null) {
            return response()->json([
                'status' => false,
                'error' => 'Brak adresu dostawy klienta'
            ], 422);
        }
        return response()->json($customerDeliveryAddress);
    }

    /**
     * Get customer invoice address.
     *
     * @param int $orderId
     * @return JsonResponse
     */
    public function getCustomerInvoiceAddress(int $orderId)
    {
        $customerInvoiceAddress = $this->orderRepository->find($orderId)->customer->addresses->where(
            'type',
            '=',
            'INVOICE_ADDRESS'
        )->first();

        if ($customerInvoiceAddress === null) {
            return response()->json([
                'status' => false,
                'error' => 'Brak adresu do faktury klienta'
            ], 422);
        }
        return response()->json($customerInvoiceAddress);
    }

    public function getCustomerStandardAddress($orderId)
    {
        return $this->orderRepository->find($orderId)->customer->addresses->where(
            'type',
            '=',
            'STANDARD_ADDRESS'
        )->first();
    }

    public function getReadyToShipFormAutocompleteData($orderId): array
    {
        $order = Order::find($orderId);
        list($isDeliveryChangeLocked, $isInvoiceChangeLocked) = $this->getLocks($order);

        if (!empty($order->dates)) {
            $orderDates = [
                "shipment_date_from" => $order->dates->customer_shipment_date_from,
                "shipment_date_to" => $order->dates->customer_shipment_date_to,
                "delivery_date_from" => $order->dates->customer_delivery_date_from,
                "delivery_date_to" => $order->dates->customer_delivery_date_to,
            ];
        }

        return array_merge(
            [
                "DELIVERY_ADDRESS" => $order->addresses()->with('country')->where('type', '=', 'DELIVERY_ADDRESS')->first(),
                "INVOICE_ADDRESS" => $order->addresses->where('type', '=', 'INVOICE_ADDRESS')->first(),
                "DELIVERY_LOCK" => $isDeliveryChangeLocked,
                "INVOICE_LOCK" => $isInvoiceChangeLocked,
            ],
            $orderDates ?? []
        );
    }

    private function getLocks($order): array
    {
        $deliveryLock[] = config('labels-map')['list']['produkt w przygotowaniu-po wyprodukowaniu magazyn kasuje etykiete'];
        $deliveryLock[] = config('labels-map')['list']['wyprodukowana'];
        $deliveryLock[] = config('labels-map')['list']['wyprodukowano czesciowo'];
        $deliveryLock[] = config('labels-map')['list']['wyslana do awizacji'];
        $deliveryLock[] = config('labels-map')['list']['awizacja przyjeta'];
        $deliveryLock[] = config('labels-map')['list']['awizacja odrzucona'];
        $deliveryLock[] = config('labels-map')['list']['awizacja brak odpowiedzi'];

        $invoiceLock[] = config('labels-map')['list']['faktura wystawiona'];
        $invoiceLock[] = config('labels-map')['list']['faktura wystawiona z odlozonym skutkiem magazynowym'];
        $isDeliveryChangeLocked = $order->labels
                ->filter(function ($label) use ($invoiceLock) {
                    return in_array($label->id, [48, 50, 52, 53]);
                })
                ->count() > 0;
        $isInvoiceChangeLocked = $order->labels
                ->filter(function ($label) use ($invoiceLock) {
                    return in_array($label->id, [121]);
                })
                ->count() > 0;
        return array($isDeliveryChangeLocked, $isInvoiceChangeLocked);
    }

    /**
     * Undocumented function
     *
     * @param UpdateOrderDeliveryAndInvoiceAddressesRequest $request
     * @param int $orderId
     * @return JsonResponse|void
     */
    public function updateOrderDeliveryAndInvoiceAddresses(
        UpdateOrderDeliveryAndInvoiceAddressesRequest $request,
        int                                           $orderId
    )
    {
        $message = [];

        try {
            $order = $this->orderRepository->find($orderId);
            list($isDeliveryChangeLocked, $isInvoiceChangeLocked) = $this->getLocks($order);

            $deliveryAddress = $order->addresses->where('type', '=', OrderAddress::TYPE_DELIVERY)->first();
            $invoiceAddress = $order->addresses->where('type', '=', OrderAddress::TYPE_INVOICE)->first();

            $order->shipment_date = $request->get('shipment_date');
            $order->dates()->updateOrCreate(
                [
                    'order_id' => $orderId,
                ],
                [
                    'customer_shipment_date_from' => Carbon::parse($request->get('customer_shipment_date_from'))->toDate(),
                    'customer_shipment_date_to' => Carbon::parse($request->get('customer_shipment_date_to'))->toDate(),
                    'customer_acceptance' => true,
                    'message' => 'Klient uzupełnił preferowane daty nadania przesyłki. Proszę o weryfikacje'
                ]
            );
            $order->save();

            if (!empty($request->get('delivery_description'))) {
                $helper = new MessagesHelper();
                $helper->orderId = $orderId;
                $helper->currentUserId = $order->customer_id;
                $helper->currentUserType = MessagesHelper::TYPE_CUSTOMER;
                $helper->createNewChat();
                $helper->addMessage($request->get('delivery_description'));
            }

            if (!$isDeliveryChangeLocked) {
                $deliveryAddress->update($request->get('DELIVERY_ADDRESS'));
                if (empty($request->get('DELIVERY_ADDRESS')['email'])) {
                    $deliveryAddress->update(['email' => $order->customer->login]);
                    $deliveryMail = $order->customer->login;
                } else {
                    $deliveryMail = $request->get('DELIVERY_ADDRESS')['email'];
                }
                $message[] = __('orders.message.delivery_address_changed');
            } else {
                $message[] = __('orders.message.delivery_address_change_failure');
            }

            if (!$isInvoiceChangeLocked) {
                $invoiceAddress->update($request->get('INVOICE_ADDRESS'));
                $message[] = __('orders.message.invoice_address_changed');
            } else {
                $message[] = __('orders.message.invoice_address_change_failure');
            }

            if ($deliveryAddress->wasChanged() || $invoiceAddress->wasChanged()) {
                $emailSendingService = new EmailSendingService();
                $emailSendingService->addNewScheduledEmail($order, EmailSetting::ADDRESS_CHANGED);
            }

            return response()->json(implode(" ", $message), 200, [], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            Log::error(
                'Problem with update customer invoice and delivery address.',
                ['exception' => $e->getMessage(), 'class' => $e->getTraceAsString(), 'line' => $e->getLine()]
            );
            die();
        }
    }

    public function updateOrderAddressEndpoint(Request $request, $orderId)
    {
        $order = Order::find($orderId);
        if ($order == null) {
            return response()->json('Zamówienie nie zostało znalezione', 404);
        }
        $data = $request->all();
        $isDeliverModificationForbidden = $order->labels()->whereIn('labels.id', [52, 53, 77])->count();
        if ($data['address_type'] === 'DELIVERY_ADDRESS' && $isDeliverModificationForbidden) {
            return response()->json('Nie można edytować', 400);
        }
        $isInvoiceModificationForbidden = $order->labels()->whereIn('labels.id', [42, 120])->count();
        if ($data['address_type'] === 'INVOICE_ADDRESS' && $isInvoiceModificationForbidden) {
            return response()->json('Nie można edytować', 400);
        }
        OrderBuilder::updateOrderAddress($order, $data['order_params'] ?? [], $data['address_type'], $data['order_params']['phone'] ?? '', 'order');
        return response()->json('Success', 200);
    }

    public function orderPackagesCancelled(Request $request, $id)
    {
        try {
            $orderPackage = OrderPackage::findOrFail($id);

            if ($request->cancelled == 'true') {
                $response = OrderPackageService::setPackageAsCancelled($orderPackage);
                $message = $response === OrderPackageService::RESPONSE_OK ? 'Przyjęto anulację paczki.' : 'Anulowanie paczki nie powiodła się. Proszę spróbować ponownie za chwilę.';
            } else {
                $orderPackage->status = PackageStatus::REJECT_CANCELLED;
                $message = 'Odrzucono anulację paczki.';
            }

            $orderPackage->update();

            return response()->json($message, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            Log::error(
                'Problem with cancelled packages.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }

    public function getAll(Request $request)
    {
        $orders = $request->user()->orders()
            ->with('status')
            ->with(['items' => function ($q) {
                $q->with(['product' => function ($w) {
                    $w->with('packing')
                        ->with('price');
                }]);
            }])
            ->with('packages', 'payments', 'labels', 'addresses', 'invoices', 'employee', 'files', 'dates', 'factoryDelivery', 'orderOffers')
            ->orderBy('id', 'desc')
            ->where('is_hidden', false)
            ->get();

        foreach ($orders as $order) {
            $order->proforma_invoice = asset(Storage::url($order->getProformStoragePathAttribute()));
            $order->total_sum = $order->getSumOfGrossValues();
            $order->bookedPaymentsSum = $order->bookedPaymentsSum();
            $userId = $request->user()->id;
            $orderButtons = ChatHelper::createButtonsArrayForOrder($order, $userId, MessagesHelper::TYPE_CUSTOMER);
            $order->buttons = $orderButtons;
            $order->user_invoices = $order->subiektInvoices;

        }

        return $orders->toJson();
    }

    public function getByToken(Request $request, $token): JsonResponse
    {
        if (empty($token)) {
            return response("Missing token", 400);
        }

        $order = Order::where('token', $token)
            ->with(['items.product' => function ($q) {
                $q->join('packings', 'products.packing_id', '=', 'packings.id')
                    ->join('prices', 'products.price_id', '=', 'prices.id')
                    ->select('products.*', 'packings.name as packing_name', 'prices.amount as price_amount');
            }])
            ->toSql();

        throw new Exception($order);

        if (!$order) {
            return response()->json("Order doesn't exist", 400);
        }

        $products = [];

        foreach ($order->items as $item) {
            if ($item->product) {
                foreach (OrderBuilder::getPriceColumns() as $column) {
                    if (property_exists($item, $column) && property_exists($item->product, $column)) {
                        $item->product->$column = $item->$column;
                    }
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
                    if (property_exists($item, $kNet) && property_exists($item->product, $kGross)) {
                        $item->product->$kGross = round($item->$kNet * $vat, 2);
                    }
                }

                if (property_exists($item, 'gross_selling_price_commercial_unit') && property_exists($item, 'quantity')) {
                    $item->product->gross_price_of_packing = $item->gross_selling_price_commercial_unit;
                    $item->product->amount = $item->quantity;
                }

                $item->product->amount = $item->quantity;

                $products[] = $item->product;
            }
        }

        return response()->json($products, 200);
    }


    public function getPaymentDetailsForOrder(Request $request, $token): JsonResponse|array
    {
        if (empty($token)) {
            return response()->json("Missing token", 400);
        }

        $order = Order::where('token', $token)->first();

        return ['total_price' => $order->total_price, 'transport_price' => $order->getTransportPrice(), 'id' => $order->id];
    }

    public function getDates(Order $order): array
    {
        /** @var OrderDates $dates */
        $dates = $order->dates;
        if (empty($dates)) {
            $order->dates()->create([
                'message' => 'Proszę o uzupełnienie dat'
            ]);
            $order->refresh();
            $dates = $order->dates;
        }
        return [
            'customer' => [
                'delivery_date_from' => $dates->getDateAttribute('customer_delivery_date_from'),
                'delivery_date_to' => $dates->getDateAttribute('customer_delivery_date_to'),
                'shipment_date_from' => $dates->getDateAttribute('customer_shipment_date_from'),
                'shipment_date_to' => $dates->getDateAttribute('customer_shipment_date_to'),
            ],
            'consultant' => [
                'delivery_date_from' => $dates->getDateAttribute('consultant_delivery_date_from'),
                'delivery_date_to' => $dates->getDateAttribute('consultant_delivery_date_to'),
                'shipment_date_from' => $dates->getDateAttribute('consultant_shipment_date_from'),
                'shipment_date_to' => $dates->getDateAttribute('consultant_shipment_date_to'),
            ],
            'warehouse' => [
                'delivery_date_from' => $dates->getDateAttribute('warehouse_delivery_date_from'),
                'delivery_date_to' => $dates->getDateAttribute('warehouse_delivery_date_to'),
                'shipment_date_from' => $dates->getDateAttribute('warehouse_shipment_date_from'),
                'shipment_date_to' => $dates->getDateAttribute('warehouse_shipment_date_to'),
            ],
            'acceptance' => [
                'customer' => $dates->customer_acceptance,
                'consultant' => $dates->consultant_acceptance,
                'warehouse' => $dates->warehouse_acceptance,
                'message' => $dates->message ?? '',
            ]
        ];
    }

    public function acceptDates(Order $order, Request $request)
    {
        $result = null;
        WorkingEventsService::createEvent(WorkingEvents::ACCEPT_DATES_EVENT, $order->id);
        if ($request->has('type') && $request->has('userType')) {
            /** @var OrderDates $dates */
            $dates = $order->dates;
            $result = $order->dates()->update([
                $request->userType . '_delivery_date_from' => $dates->getDateAttribute($request->type . '_delivery_date_from'),
                $request->userType . '_delivery_date_to' => $dates->getDateAttribute($request->type . '_delivery_date_to'),
                $request->userType . '_shipment_date_from' => $dates->getDateAttribute($request->type . '_shipment_date_from'),
                $request->userType . '_shipment_date_to' => $dates->getDateAttribute($request->type . '_shipment_date_to'),
                $request->userType . '_acceptance' => true,
                'message' => __('order_dates.' . $request->userType) . ' <strong>zaakceptował</strong> daty dotyczące przesyłki. Proszę o weryfikacje i akceptacje'

            ]);
        }
        if ($result) {
            $order->dates->refresh();
            return response(json_encode([
                'acceptance' => [
                    'customer' => $order->dates->customer_acceptance,
                    'consultant' => $order->dates->consultant_acceptance,
                    'warehouse' => $order->dates->warehouse_acceptance,
                    'message' => $order->dates->message,
                ],
                $request->userType => [
                    'delivery_date_from' => $dates->getDateAttribute($request->type . '_delivery_date_from'),
                    'delivery_date_to' => $dates->getDateAttribute($request->type . '_delivery_date_to'),
                    'shipment_date_from' => $dates->getDateAttribute($request->type . '_shipment_date_from'),
                    'shipment_date_to' => $dates->getDateAttribute($request->type . '_shipment_date_to'),
                ]
            ]), 200);
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('order_dates.messages.error')
        ]), 500);
    }

    public function updateDates(Order $order, Request $request)
    {
        $result = null;
        WorkingEventsService::createEvent(WorkingEvents::UPDATE_DATES_EVENT, $order->id);
        if ($request->has('type')) {
            $order->dates->resetAcceptance();
            $result = $order->dates()->update([
                $request->type . '_shipment_date_from' => $request->shipmentDateFrom,
                $request->type . '_shipment_date_to' => $request->shipmentDateTo,
                $request->type . '_delivery_date_from' => $request->deliveryDateFrom,
                $request->type . '_delivery_date_to' => $request->deliveryDateTo,
                $request->type . '_acceptance' => true,
                'message' => __('order_dates.' . $request->type) . ' <strong>zmodyfikował</strong> daty dotyczące przesyłki. Proszę o weryfikacje i akceptacje'
            ]);
        }

        if ($result) {
            $order->dates->refresh();
            return response(json_encode([
                $request->type => [
                    'shipment_date_from' => $request->shipmentDateFrom,
                    'shipment_date_to' => $request->shipmentDateTo,
                    'delivery_date_from' => $request->deliveryDateFrom,
                    'delivery_date_to' => $request->deliveryDateTo,
                ],
                'acceptance' => [
                    $request->type => true,
                    'message' => $order->dates->message,
                ]
            ]), 200);
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('order_dates.messages.error')
        ]), 500);
    }

    public function acceptDatesAsCustomer(Order $order, Request $request)
    {
        $result = $order->dates()->update([
            'customer_shipment_date_from' => $order->dates->consultant_shipment_date_from,
            'customer_shipment_date_to' => $order->dates->consultant_shipment_date_to,
            'customer_delivery_date_from' => $order->dates->consultant_delivery_date_from,
            'customer_delivery_date_to' => $order->dates->consultant_delivery_date_to,
            'customer_acceptance' => true,
            'message' => 'Konsultant <strong>zaakceptował</strong> daty dotyczące przesyłki w imieniu klienta.'
        ]);

        if ($request->get('chatId') && $result) {
            $helper = new MessagesHelper();
            $helper->orderId = $order->id;
            $helper->currentUserType = MessagesHelper::TYPE_USER;
            if (empty($request->chatId)) {
                $helper->createNewChat();
            } else {
                $helper->chatId = $request->chatId;
            }

            if (empty($helper->getCurrentUser())) {
                $helper->currentUserId = $helper->getChat()->chatUsers->first()->user_id;
            }
            $helper->addMessage('Konsultant zaakceptował daty w imieniu klienta.');
        }

        if ($result) {
            $order->dates->refresh();
            return response(json_encode([
                'customer' => [
                    'shipment_date_from' => $order->dates->getDateAttribute('customer_shipment_date_from'),
                    'shipment_date_to' => $order->dates->getDateAttribute('customer_shipment_date_to'),
                    'delivery_date_from' => $order->dates->getDateAttribute('customer_delivery_date_from'),
                    'delivery_date_to' => $order->dates->getDateAttribute('customer_delivery_date_to'),
                ],
                'acceptance' => [
                    'customer' => $order->dates->customer_acceptance,
                    'consultant' => $order->dates->consultant_acceptance,
                    'warehouse' => $order->dates->warehouse_acceptance,
                    'message' => $order->dates->message,
                ]
            ]), 200);
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('order_dates.messages.error')
        ]), 500);
    }

    public function declineProform(
        DeclineProformRequest $request,
        int                   $orderId
    )
    {
        if (!($order = $this->orderRepository->find($orderId))) {
            return [];
        }

        $order->labels_log .= Order::formatMessage(Auth::user(), $request->description);
        $order->save();
        $prev = [];
        AddLabelService::addLabels($order, [Label::FINAL_CONFIRMATION_DECLINED, Label::MASTER_MARK], $prev, [], Auth::user()->id);

        return response()->json(__('orders.message.update'), 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function acceptDeliveryInvoiceData($orderId): array|JsonResponse
    {
        if (!($order = $this->orderRepository->find($orderId))) {
            return [];
        }

        $prev = [];
        AddLabelService::addLabels($order, [116, 137], $prev, [], Auth::user()->id);

        return response()->json(__('orders.message.update'), 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function acceptReceivingOrder(AcceptReceivingOrderRequest $request, $orderId): JsonResponse|array
    {
        if (!($order = $this->orderRepository->find($orderId))) {
            return [];
        }

        $labelId = match ($request->invoice_day) {
            'standard' => Label::ORDER_RECEIVED_INVOICE_STANDARD,
            'today' => Label::ORDER_RECEIVED_INVOICE_TODAY,
            default => 0,
        };
        if ($labelId > 0) {
            $prev = [];
            AddLabelService::addLabels($order, [$labelId], $prev, [], Auth::user()->id);
        }

        return response()->json(__('orders.message.update'), 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Return countries.
     */
    public function countries(): JsonResponse
    {
        return response()->json(Country::all());
    }

    public function uploadProofOfPayment(Request $request): JsonResponse
    {
        /** @var Order $order */
        $order = Order::query()->find($request->id);

        if (!$order) return response()->json(['errorMessage' => 'Nie można znaleźć zamówienia'], 400);
        if ($order->customer_id != $request->user()->id) return response()->json(['errorMessage' => 'Nie twoje zamówienie'], 400);

        $ordersController = App::make(OrdersControllerApp::class);
        $ordersController->addFile($request, $order->id);

        $prev = [];
        AddLabelService::addLabels($order, [Label::PROOF_OF_PAYMENT_UPLOADED], $prev, [], Auth::user()->id);

        return response()->json('success', 200);
    }

    /**
     * Send offer to customer api service
     *
     * @param int $id
     *
     * @return ResponseFactory|Application|JsonResponse|Response
     */
    public function sendOfferToCustomer(int $id)
    {
        $order = $this->orderRepository->with(['customer', 'items', 'labels'])->find($id);
        try {
            if (empty($order)) {
                throw new OrderNotFoundException();
            }

            $productsArray = [];
            foreach ($order->items as $item) {
                $productsArray[] = $item->product_id;
            }

            $productsVariation = $this->productService->getVariations($order);
            $allProductsFromSupplier = [];
            $tempVariationCounter = [];
            foreach ($productsVariation as $key => $variation) {
                $variations = current($variation);
                if (isset($tempVariationCounter[current($variations)['variation_group']])) {
                    $tempVariationCounter[current($variations)['variation_group']] += 1;
                } else {
                    $tempVariationCounter[current($variations)['variation_group']] = 1;
                }

                foreach ($variation as $item) {
                    if ($item['variation_group'] == null) {
                        continue;
                    }
                    if (isset($allProductsFromSupplier[$item['product_name_supplier']])) {
                        $sum = (float)$allProductsFromSupplier[$item['product_name_supplier']][$item['variation_group']]['sum'];
                        $count = (float)$allProductsFromSupplier[$item['product_name_supplier']][$item['variation_group']]['count'];
                        $sum += $item['sum'];
                        $count += 1;
                    } else {
                        $sum = $item['sum'];
                        $count = 1;
                    }
                    if ($count < $tempVariationCounter[$item['variation_group']] || $sum == 0) {
                        continue;
                    }

                    $arr = [
                        'missed_product' => $count < $tempVariationCounter[$item['variation_group']],
                        'count' => $count,
                        'sum' => $sum,
                        'different' => number_format($order->total_price - $sum, 2, '.', ''),
                        'radius' => $item['radius'],
                        'phone' => $item['phone'],
                        'product_name_supplier' => $item['product_name_supplier'],
                        'review' => $item['review'],
                        'quality' => $item['quality'],
                        'quality_to_price' => $item['quality_to_price'],
                        'comments' => $item['comments'],
                        'warehouse_property' => $item['warehouse_property'],
                        'value_of_the_order_for_free_transport' => number_format(
                            (float)$item['value_of_the_order_for_free_transport'] - $order->total_price,
                            2,
                            '.',
                            ''
                        ) <= 0 ? 'Darmowy transport!' : number_format(
                            (float)$item['value_of_the_order_for_free_transport'] - $order->total_price,
                            2,
                            '.',
                            ''
                        )

                    ];
                    $allProductsFromSupplier[$item['product_name_supplier']][$item['variation_group']] = $arr;
                }
            }

            if (!empty($allProductsFromSupplier)) {
                $allProductsFromSupplier = collect($allProductsFromSupplier)->sortBy('different', 1, true);
            } else {
                $allProductsFromSupplier = null;
            }
            $productPacking = $this->productPackingRepository->findWhereIn('product_id', $productsArray);

            if (!strpos($order->customer->login, 'allegromail.pl')) {
                Mailer::create()
                    ->to($order->customer->login)
                    ->send(new SendOfferToCustomerMail(
                        'Oferta nr: ' . $order->id,
                        $order,
                        $productsVariation,
                        $allProductsFromSupplier,
                        $productPacking
                    ));
            }
        } catch (Throwable $exception) {
            return response(json_encode([
                'status' => false,
                'error_code' => 500,
                'error_message' => $exception->getMessage()
            ]), 500);
        }

        return response()->json('Oferta została wysłana', 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function getLatestDeliveryInfo(Order $order): JsonResponse
    {
        $deliveryInfos = $order->customer->orders()->get();
        foreach ($deliveryInfos as $deliveryInfo) {
            $deliveryInfo->adress = $deliveryInfo->getDeliveryAddress();
        }

        return response()->json($deliveryInfos, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function getLatestInvoiceInfo(Order $order): JsonResponse
    {
        $invoiceInfos = $order->customer->orders()->get();
        foreach ($invoiceInfos as $invoiceInfo) {
            $invoiceInfo->adress = $invoiceInfo->getInvoiceAddress();
        }

        return response()->json($invoiceInfos, 200, [], JSON_UNESCAPED_UNICODE);

    }

    /**
     * @param Order $order
     *
     * @return JsonResponse
     */
    public function moveToUnactive(Order $order): JsonResponse
    {
        try {
            $order->labels()->attach(225);
        } catch (Throwable $exception) {
            return response()->json([
                'status' => false,
                'error_code' => 500,
                'error_message' => $exception->getMessage()
            ], 500);
        }

        return response()->json($order, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param Order $order
     * @param ScheduleOrderReminderRequest $request
     *
     * @return JsonResponse
     */
    public function scheduleOrderReminder(Order $order, ScheduleOrderReminderRequest $request): JsonResponse
    {
        $data = $request->validated();

        Order::query()->update([
            'reminder_date' => $data['dateTime']
        ]);

        $order->labels()->attach(224);

        $date = Carbon::createFromFormat('Y-m-d H:i', $data['dateTime']);

        SendReminderAboutOfferJob::dispatch($order)->delay($date);

        return response()->json([
            'status' => true,
            'message' => 'Przypomnienie zostało zaplanowane',
            'date' => $date->format('Y-m-d H:i')
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
