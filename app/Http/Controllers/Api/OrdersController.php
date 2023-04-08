<?php

namespace App\Http\Controllers\Api;


use App\Domains\DelivererPackageImport\Exceptions\OrderNotFoundException;
use App\Entities\Country;
use App\Entities\FirmSource;
use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderAddress;
use App\Entities\OrderDates;
use App\Entities\WorkingEvents;
use App\Enums\PackageStatus;
use App\Helpers\BackPackPackageDivider;
use App\Helpers\ChatHelper;
use App\Helpers\GetCustomerForAdminEdit;
use App\Helpers\GetCustomerForNewOrder;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPriceCalculator;
use App\Helpers\TransportSumCalculator;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OrdersController as OrdersControllerApp;
use App\Http\Requests\Api\Orders\AcceptReceivingOrderRequest;
use App\Http\Requests\Api\Orders\DeclineProformRequest;
use App\Http\Requests\Api\Orders\StoreOrderMessageRequest;
use App\Http\Requests\Api\Orders\StoreOrderRequest;
use App\Http\Requests\Api\Orders\UpdateOrderDeliveryAndInvoiceAddressesRequest;
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
use App\Services\Label\AddLabelService;
use App\Services\OrderPackageService;
use App\Services\ProductService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Facades\Mailer;
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
     * @param OrderPackageRepository $orderPackageRepository
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

    public function newOrder(StoreOrderRequest $request, ProductService $productService)
    {
        $data = $request->all();
        DB::beginTransaction();
        $customer = auth()->guard('api')->user();

        try {
            $orderBuilder = new OrderBuilder();
            $orderBuilder
                ->setPackageGenerator(new BackPackPackageDivider())
                ->setPriceCalculator(new OrderPriceCalculator())
                ->setProductService($productService);
            if (empty($data['cart_token'])) {
                $orderBuilder->setTotalTransportSumCalculator(new TransportSumCalculator())
                    ->setUserSelector(new GetCustomerForNewOrder());
            } else {
                $orderBuilder->setUserSelector(new GetCustomerForAdminEdit());
            }
            $builderData = $orderBuilder->newStore($data, $customer);
            DB::commit();
            
            $order = Order::find($builderData['id']);
            $builderData['token'] = $order->getToken();
            $firmSource = FirmSource::byFirmAndSource(env('FIRM_ID'), 2)->first();
            $order->firm_source_id = $firmSource ? $firmSource->id : null;
            $order->save();

            return response()->json($builderData);
        } catch (Exception $e) {
            DB::rollBack();
            if (empty($this->error_code)) {
                $this->error_code = $e->getMessage();
            }
            $message = $this->errors[$this->error_code] ?? $e->getMessage();
            Log::error(
                "Problem with create new order: [{$this->error_code}] $message" . '. Trace log: ' . $e->getTraceAsString(),
                ['request' => $data, 'class' => $e->getFile(), 'line' => $e->getLine()]
            );
            $message = $this->errors[$this->error_code] ?? $this->defaultError;
            return response(json_encode([
                'error_code' => $this->error_code,
                'error_message' => $message
            ]), $this->error_code ? 400 : 500);
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

    public function getReadyToShipFormAutocompleteData($orderId)
    {
        $order = $this->orderRepository->find($orderId);
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
                ->filter(function ($label) use ($deliveryLock) {
                    return in_array($label->id, $deliveryLock);
                })
                ->count() > 0;
        $isInvoiceChangeLocked = $order->labels
                ->filter(function ($label) use ($invoiceLock) {
                    return in_array($label->id, $invoiceLock);
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
                // dispatch(new OrderProformSendMailJob($order, setting('allegro.address_changed_msg')));
            }

            return response()->json(implode(" ", $message), 200, [], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            Log::error(
                'Problem with update customer invoice and delivery address.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => $e->getLine()]
            );
            die();
        }
    }

    public function updateOrderAddressEndpoint(Request $request, $orderId)
    {
        $order = Order::find($orderId);
        if ($order == null) {
            return response('Zamówienie nie zostało znalezione', 404);
        }
        $data = $request->all();
        $isDeliverModificationForbidden = $order->labels()->whereIn('labels.id', [52, 53, 77])->count();
        if ($data['address_type'] === 'DELIVERY_ADDRESS' && $isDeliverModificationForbidden) {
            return response('Nie można edytować', 400);
        }
        $isInvoiceModificationForbidden = $order->labels()->whereIn('labels.id', [42, 120])->count();
        if ($data['address_type'] === 'INVOICE_ADDRESS' && $isInvoiceModificationForbidden) {
            return response('Nie można edytować', 400);
        }
        OrderBuilder::updateOrderAddress($order, $data['order_params'] ?? [], $data['address_type'], $data['order_params']['phone'] ?? '', 'order');
        return response('Success', 200);
    }

    public function orderPackagesCancelled(Request $request, $id)
    {
        try {
            $orderPackage = $this->orderPackageRepository->find($id);

            if (empty($orderPackage)) {
                Log::info(
                    'Problem with find orderPackage item with id =' . $id,
                    ['class' => get_class($this), 'line' => __LINE__]
                );
                abort(404);
            }

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

    public function getByToken(Request $request, $token)
    {
        if (empty($token)) {
            return response("Missing token", 400);
        }
        $order = Order::where('token', $token)
            ->with(['items' => function ($q) {
                $q->with(['product' => function ($q) {
                    $q->join('product_packings', 'products.id', '=', 'product_packings.product_id')
                        ->leftJoin('product_prices', 'product_packings.product_id', '=', 'product_prices.product_id');
                }]);
            }])
            ->first();
        if (!$order) {
            return response("Order doesn't exist", 400);
        }

        $products = [];

        foreach ($order->items as $item) {
            foreach (OrderBuilder::getPriceColumns() as $column) {
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

            $item->product->gross_price_of_packing = $item->gross_selling_price_commercial_unit;
            $item->product->amount = $item->quantity;

            $products[] = $item->product;
        }

        return response(json_encode($products));
    }

    public function getPaymentDetailsForOrder(Request $request, $token)
    {
        if (empty($token)) {
            return response("Missing token", 400);
        }

        $order = Order::where('token', $token)->first();
        return ['total_price' => $order->total_price, 'transport_price' => $order->getTransportPrice(), 'id' => $order->id];
    }

    public function getDates(Order $order)
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
        WorkingEvents::createEvent(WorkingEvents::ACCEPT_DATES_EVENT, $order->id);
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
        WorkingEvents::createEvent(WorkingEvents::UPDATE_DATES_EVENT, $order->id);
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
                              $orderId
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

    public function acceptDeliveryInvoiceData($orderId)
    {
        if (!($order = $this->orderRepository->find($orderId))) {
            return [];
        }

        $prev = [];
        AddLabelService::addLabels($order, [116, 137], $prev, [], Auth::user()->id);

        return response()->json(__('orders.message.update'), 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function acceptReceivingOrder(AcceptReceivingOrderRequest $request, $orderId)
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
    public function countries()
    {
        return response()->json(Country::all());
    }

    public function uploadProofOfPayment(Request $request)
    {
        $orderId = $request->id;
        /** @var Order $order */
        $order = Order::query()->find($orderId);
        if (!$order) return response(['errorMessage' => 'Nie można znaleźć zamówienia'], 400);
        if ($order->customer_id != $request->user()->id) return response(['errorMessage' => 'Nie twoje zamówienie'], 400);
        $ordersController = App::make(OrdersControllerApp::class);
        $ordersController->addFile($request, $orderId);
        $prev = [];
        AddLabelService::addLabels($order, [Label::PROOF_OF_PAYMENT_UPLOADED], $prev, [], Auth::user()->id);
        return response('success', 200);
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

    public function getLatestDeliveryInfo(Order $order)
    {
        $deliveryInfos = $order->customer->orders()->get();
        foreach ($deliveryInfos as $deliveryInfo) {
            $deliveryInfo->adress = $deliveryInfo->getDeliveryAddress();
        }

        return response()->json($deliveryInfos, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function getLatestInvoiceInfo(Order $order)
    {
        $invoiceInfos = $order->customer->orders()->get();
        foreach ($invoiceInfos as $invoiceInfo) {
            $invoiceInfo->adress = $invoiceInfo->getInvoiceAddress();
        }

        return response()->json($invoiceInfos, 200, [], JSON_UNESCAPED_UNICODE);

    }
}
