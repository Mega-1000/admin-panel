<?php

namespace App\Jobs;

use App\Entities\AllegroOrder;
use App\Entities\Country;
use App\Entities\Customer;
use App\Entities\CustomerAddress;
use App\Entities\FirmSource;
use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderAddress;
use App\Entities\OrderItem;
use App\Entities\OrderPackage;
use App\Entities\OrderPayment;
use App\Entities\PackageTemplate;
use App\Entities\Product;
use App\Entities\ProductStock;
use App\Entities\Task;
use App\Entities\TaskSalaryDetails;
use App\Entities\Warehouse;
use App\Enums\OrderPaymentsEnum;
use App\Helpers\Helper;
use App\Helpers\LabelsHelper;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPackagesDataHelper;
use App\Helpers\StringHelper;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\AllegroOrderService;
use App\Services\EmailSendingService;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\OrderAddressService;
use App\Services\ProductService;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Throwable;

/**
 * Allegro order synchro
 */
class AllegroOrderSynchro implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    protected ?int $userId;
    private CustomerRepository $customerRepository;
    private AllegroOrderService $allegroOrderService;
    private ProductRepository $productRepository;
    private ProductService $productService;
    private EmailSendingService $emailSendingService;
    private OrderRepository $orderRepository;
    private OrderPackagesDataHelper $orderPackagesDataHelper;
    private float $tax;
    private bool $synchronizeAll;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(bool $synchronizeAll = false)
    {
        $this->synchronizeAll = $synchronizeAll;
        $this->userId = Auth::user()?->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        if (Auth::user() === null && $this->userId !== null) {
            Auth::loginUsingId($this->userId);
        }
        $this->customerRepository = app(CustomerRepository::class);
        $this->allegroOrderService = app(AllegroOrderService::class);
        $this->productRepository = app(ProductRepository::class);
        $this->productService = app(ProductService::class);
        $this->orderRepository = app(OrderRepository::class);
        $this->orderPackagesDataHelper = app(OrderPackagesDataHelper::class);
        $this->emailSendingService = app(EmailSendingService::class);
        $this->tax = (float)(1 + config('orders.vat'));

        $cancelledOrders = [];
        if ($this->synchronizeAll) {
            $allegroOrders = $this->allegroOrderService->getOrdersOutsideSystem();
        } else {
            $allegroOrders = $this->allegroOrderService->getPendingOrders();
            $cancelledOrders = $this->allegroOrderService->getCancelledOrders();
        }
        $allegroOrders = array_merge($allegroOrders, $cancelledOrders);

        foreach (array_reverse($allegroOrders) as $allegroOrder) {
            try {
                $orderModel = Order::query()->where('allegro_form_id', $allegroOrder['id'])->first();
                if ($orderModel !== null) {
                    continue;
                }
                $orderStatus = $allegroOrder['status'] ?? AllegroOrderService::STATUS_CANCELLED;
                $paidAmount = $allegroOrder['payment']['paidAmount']['amount'] ?? 0;

                if ($orderStatus === AllegroOrderService::STATUS_CANCELLED) {
                    if ($paidAmount <= 0) {
                        continue;
                    }
                }

                if ($this->checkAllegroDeliveryAddressExisting($allegroOrder) === false) {
                    Log::error(
                        'Not existing address data in Allegro Order',
                        $allegroOrder
                    );
                    continue;
                }

                DB::beginTransaction();
                $orderModel = AllegroOrder::query()->firstOrNew(['order_id' => $allegroOrder['id']]);
                $orderModel->order_id = $allegroOrder['id'];
                $orderModel->buyer_email = $allegroOrder['buyer']['email'];
                $orderModel->save();

                $order = new Order();
                $customer = $this->findOrCreateCustomer($allegroOrder);
                $order->customer_id = $customer->id;
                $order->allegro_form_id = $allegroOrder['id'];
                $order->status_id = 1;
                $order->allegro_operation_date = $allegroOrder['lineItems'][0]['boughtAt'];
                $order->allegro_additional_service = $allegroOrder['delivery']['method']['name'];
                $order->preferred_invoice_date = Carbon::now();
                $order->payment_channel = $allegroOrder['payment']['provider'];
                $order->allegro_payment_id = $allegroOrder['payment']['id'];
                $order->saveQuietly();

                $order->orderDates()->create([
                    'customer_delivery_date_from' => $allegroOrder['delivery']['time']['from'],
                    'customer_delivery_date_to' => $allegroOrder['delivery']['time']['to'],
                    'consultant_delivery_date_from' => $allegroOrder['delivery']['time']['from'],
                    'consultant_delivery_date_to' => $allegroOrder['delivery']['time']['to'],
                ]);

                $this->emailSendingService->addNewScheduledEmail($order);

                if ($allegroOrder['status'] === $this->allegroOrderService::STATUS_CANCELLED) {
                    $prev = [];
                    AddLabelService::addLabels($order, [176], $prev, []);
                }

                if ($allegroOrder['messageToSeller'] !== null) {
                    $this->createChat($order->id, $customer->id, $allegroOrder['messageToSeller']);
                    $order->labels()->attach(MessagesHelper::MESSAGE_YELLOW_LABEL_ID);
                }

                list($orderItems, $undefinedProductSymbol) = $this->mapItems($allegroOrder['lineItems']);

                if ($undefinedProductSymbol) {
                    $order->consultant_notices = 'Nie znaleziono produktu o symbolu ' . $undefinedProductSymbol['id'];
                    $prev = [];
                    AddLabelService::addLabels($order, [Label::WAREHOUSE_MARK], $prev, [], Auth::user()?->id);
                }

                $this->saveOrderItems($orderItems, $order);

                $this->savePayments($order, $allegroOrder['payment']);

                if (!Helper::phoneIsCorrect($allegroOrder['buyer']['phoneNumber'])) {
                    if (!Helper::phoneIsCorrect($allegroOrder['buyer']['address']['phoneNumber'] ?? null)) {
                        $allegroOrder['buyer']['address']['phoneNumber'] = $allegroOrder['delivery']['address']['phoneNumber'];
                    }
                    $allegroOrder['buyer']['phoneNumber'] = $allegroOrder['buyer']['address']['phoneNumber'];
                }
                $invoiceAddress = ($allegroOrder['invoice']['address'] !== null) ? $allegroOrder['invoice'] : $allegroOrder['buyer'];
                $invoiceAddress['address']['phoneNumber'] = $allegroOrder['buyer']['phoneNumber'];
                $this->createOrUpdateCustomerAddress($customer, $allegroOrder['buyer']);

                $this->createOrUpdateCustomerAddress($customer, $invoiceAddress, CustomerAddress::ADDRESS_TYPE_INVOICE);
                if (array_key_exists('pickupPoint', $allegroOrder['delivery']) && $allegroOrder['delivery']['pickupPoint'] !== null) {
                    $allegroOrder['delivery']['address']['firstName'] = 'Paczkomat';
                    $allegroOrder['delivery']['address']['lastName'] = $allegroOrder['delivery']['pickupPoint']['id'];
                    if (!array_key_exists('address', $allegroOrder['delivery']['pickupPoint'])) {
                        Log::info('PickupPoint address error: order: ' . $order->id . ', AllId' . $allegroOrder['id'], $allegroOrder['delivery']);
                    } else {
                        $allegroOrder['delivery']['address'] = array_merge($allegroOrder['delivery']['address'], $allegroOrder['delivery']['pickupPoint']['address']);
                    }
                }
                $this->createOrUpdateOrderAddress($order, $allegroOrder['buyer'], $allegroOrder['delivery']['address'] ?? []);

                $this->createOrUpdateOrderAddress($order, $allegroOrder['buyer'], $invoiceAddress, OrderAddress::TYPE_INVOICE);

                $orderDeliveryAddress = OrderAddress::where("order_id", $order->id)
                    ->where('type', 'DELIVERY_ADDRESS')
                    ->first();

                $orderAddressService = new OrderAddressService();
                $orderAddressService->addressIsValid($orderDeliveryAddress);
                $orderDeliveryAddressErrors = $orderAddressService->errors();

                if ($orderDeliveryAddressErrors->any()) {
                    $order->labels_log .= Order::formatMessage(null, implode(' ', $orderDeliveryAddressErrors->all(':message')));
                    $order->saveQuietly();

                }

                if (!Helper::phoneIsCorrect($orderDeliveryAddress?->phone ?? '')) {
                    $order->labels_log .= Order::formatMessage(null, 'ebudownictwo@wp.pl 691801594 55-200');
                    $order->labels()->attach(176);
                    $order->saveQuietly();
                }

                // order package
                $this->addOrderPackage($order, $allegroOrder['delivery']);
                $order->shipment_price_for_client = $allegroOrder['delivery']['cost']['amount'] ?? 0;

                $order->total_price = $allegroOrder['summary']['totalToPay']['amount'] ?? 0;
                $firmSource = FirmSource::byFirmAndSource(config('orders.firm_id'), 1)->first();
                $order->firm_source_id = $firmSource ? $firmSource->id : null;

                $user = User::where('name', '001')->first();
                $order->employee()->associate($user);
                $withWarehouse = (new Collection($orderItems))->filter(function ($prod) {
                    return !empty($prod->packing->warehouse_physical);
                });
                $warehouseSymbol = $withWarehouse->first()->packing->warehouse_physical ?? ImportOrdersFromSelloJob::DEFAULT_WAREHOUSE;
                $warehouse = Warehouse::where('symbol', $warehouseSymbol)->first();
                $order->warehouse()->associate($warehouse);
                $order->setDefaultDates('allegro');

                $order->saveQuietly();
                $this->addLabels($order);

                if (($orderInvoiceAddress = $order->getInvoiceAddress())
                    && ($orderDeliveryAddress = $order->getDeliveryAddress())) {
                    $orderAddressService = new OrderAddressService();

                    $orderAddressService->addressIsValid($orderInvoiceAddress);
                    $orderInvoiceAddressErrors = $orderAddressService->errors();

                    $orderAddressService->addressIsValid($orderDeliveryAddress);
                    $orderDeliveryAddressErrors = $orderAddressService->errors();
                    if (!$orderInvoiceAddressErrors->any() && !$orderDeliveryAddressErrors->any()) {
                        $order->labels()->attach(39);
                    }
                }
                $prev = [];
                AddLabelService::addLabels($order, [177], $prev, [], Auth::user()?->id);
                $this->allegroOrderService->setSellerOrderStatus($allegroOrder['id'], AllegroOrderService::STATUS_PROCESSING);
                DB::commit();
            } catch (Throwable $ex) {
                DB::rollBack();
                Log::error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString(), [
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'orderId' => (isset($order)) ? $order->id : $allegroOrder['id'],
                    'allegroOrder' => $allegroOrder,
                ]);
            }
        }
    }

    private function checkAllegroDeliveryAddressExisting(array $allegroOrder): bool
    {
        if (array_key_exists('delivery', $allegroOrder) && array_key_exists('address', $allegroOrder['delivery'] ?? []) && count($allegroOrder['delivery']['address'] ?? []) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Find customer by buyer
     *
     * @param array $allegroOrder
     * @return Customer
     * @throws Exception
     */
    private function findOrCreateCustomer(array $allegroOrder): Customer
    {
        $buyer = $allegroOrder['buyer'];
        $deliveryAddress = $allegroOrder['delivery']['address'] ?? [];
        $buyerEmail = $buyer['email'];

        if (preg_match('/\+([a-zA-Z0-9]+)@/', $buyer['email'], $matches)) {
            $buyerEmail = str_replace('+' . $matches[1], '', $buyer['email']);
        }

        $customer = Customer::where('login', $buyerEmail)->orWhere('nick_allegro', $buyer['login'])->first();

        if ($customer && array_key_exists('phoneNumber', $deliveryAddress) && !empty($deliveryAddress['phoneNumber'])) {
            $customer->password = Hash::make($deliveryAddress['phoneNumber']);
            $customer->saveQuietly();
        }

        if ($buyer['phoneNumber'] !== null && $buyer['phoneNumber'] !== 'brak' && Helper::phoneIsCorrect($buyer['phoneNumber'])) {
            $customerPhone = $buyer['phoneNumber'];
        } elseif ($deliveryAddress['phoneNumber'] !== null && Helper::phoneIsCorrect($deliveryAddress['phoneNumber'])) {
            $customerPhone = $deliveryAddress['phoneNumber'];
        } else {
            throw new Exception('No or incorrect phone number ' . PHP_EOL . ' Buyer phone number: ' . ($buyer['phoneNumber'] ?? '') . PHP_EOL . 'Delivery address phone number: ' . ($deliveryAddress['phoneNumber'] ?? ''));
        }

        $customerPhone = str_replace('+48', '', $customerPhone);
        if ($customer === null) {
            $customer = new Customer();
            $customer->password = $customer->generatePassword($customerPhone);
        } else {
            if (!Hash::check($customerPhone, $customer->password)) {
                $customer->password = $customer->generatePassword($customerPhone);
                $customer->save();
            }
        }
        $customer->login = $buyerEmail;
        $customer->nick_allegro = $buyer['login'];
        $customer->password = $customer->generatePassword($customerPhone);
        $customer->save();
        return $customer;
    }

    /**
     * Create chat
     *
     * @param int $orderId
     * @param int $customerId
     * @param string $customerNotices
     *
     * @return void
     */
    private function createChat(int $orderId, int $customerId, string $customerNotices): void
    {
        $helper = new MessagesHelper();
        $helper->orderId = $orderId;
        $helper->currentUserId = $customerId;
        $helper->currentUserType = MessagesHelper::TYPE_CUSTOMER;
        try {
            $helper->createNewChat();
            $helper->addMessage($customerNotices);
        } catch (Throwable $ex) {
            Log::error($ex->getMessage());
        }
    }

    /**
     * Map items
     *
     * @param array $items
     *
     * @return array
     */
    private function mapItems(array $items): array
    {
        $products = [];
        $undefinedProductSymbol = null;
        try {
            foreach ($items as $item) {
                if ($item['offer']['external'] !== null) {
                    $symbol = explode('-', $item['offer']['external']['id']);

                    preg_match('/[A-Z]/', end($symbol), $match);
                    $quantityPrefix = (empty($match)) ? false : $match[0];

                    if ($quantityPrefix && strpos(end($symbol), $quantityPrefix) !== false) {
                        $quantity = (int)(explode($quantityPrefix, end($symbol))[1]);
                    } else {
                        $quantity = false;
                    }

                    $newSymbol = [$symbol[0], $symbol[1], '0'];
                    $newSymbol = join('-', $newSymbol);

                    $product = Product::query()->where('symbol', '=', $newSymbol)->first();
                }
                if (empty($product)) {
                    $product = Product::getDefaultProduct();
                    $undefinedProductSymbol = $item['offer']['external'];
                }

                if ($product?->stock()?->exists() !== true) {
                    ProductStock::query()->create([
                        'product_id' => $product->id,
                        'quantity' => 0,
                        'min_quantity' => null,
                        'unit' => null,
                        'start_quantity' => null,
                        'number_on_a_layer' => null
                    ]);
                }

                if (!empty($quantity)) {
                    $product->type = 'multiple';
                    $product->tt_quantity = $quantity * $item['quantity'];
                } else {
                    $product->tt_quantity = $item['quantity'];
                }
                $product->price_override = [
                    'gross_selling_price_commercial_unit' => round(
                        ($item['quantity'] * (float)$item['price']['amount']) / $product->tt_quantity,
                        2
                    ),
                    'net_selling_price_commercial_unit' => round(
                        ($item['quantity'] * (float)$item['price']['amount']) / $product->tt_quantity / $this->tax,
                        2
                    )
                ];

                $products[] = $product;
            }
        } catch (Throwable $ex) {
            Log::error($ex->getMessage());
        }
        return [$products, $undefinedProductSymbol];
    }

    /**
     * @param Order $order
     *
     * @return void
     */
    private function addLabels(Order $order): void
    {
        $preventionArray = [];
        RemoveLabelService::removeLabels($order, [LabelsHelper::FINISH_LOGISTIC_LABEL_ID], $preventionArray, [LabelsHelper::TRANSPORT_SPEDITION_INIT_LABEL_ID], Auth::user()?->id);
        RemoveLabelService::removeLabels($order, [LabelsHelper::TRANSPORT_SPEDITION_INIT_LABEL_ID], $preventionArray, [], Auth::user()?->id);
        RemoveLabelService::removeLabels($order, [LabelsHelper::WAIT_FOR_SPEDITION_FOR_ACCEPT_LABEL_ID], $preventionArray, [], Auth::user()?->id);

        if ($order->warehouse->id == Warehouse::OLAWA_WAREHOUSE_ID) {
            RemoveLabelService::removeLabels($order, [LabelsHelper::VALIDATE_ORDER], $preventionArray, [LabelsHelper::WAIT_FOR_WAREHOUSE_TO_ACCEPT], Auth::user()?->id);
            $order->createNewTask(5);

            return;
        }

        RemoveLabelService::removeLabels($order, [LabelsHelper::VALIDATE_ORDER], $preventionArray, [LabelsHelper::SEND_TO_WAREHOUSE_FOR_VALIDATION], Auth::user()?->id);
    }

    /**
     * Save order items.
     *
     * @param array $allegroOrderItems
     * @param Order $order
     *
     * @return void
     * @throws Exception
     */
    private function saveOrderItems(array $allegroOrderItems, Order $order): void
    {
        $weight = 0;
        foreach ($allegroOrderItems as $allegroOrderItem) {
            $price = $allegroOrderItem->price;
            if (!$allegroOrderItem || !$price) {
                throw new Exception('wrong_product_id');
            }
            $getStockProduct = $this->productService->getStockProduct($allegroOrderItem->id);

            $orderItem = new OrderItem();
            $orderItem->quantity = $allegroOrderItem->tt_quantity;
            $orderItem->product_id = $getStockProduct ? $getStockProduct->id : $allegroOrderItem->id;
            if (!empty($allegroOrderItem->weight_trade_unit)) {
                $weight += $allegroOrderItem->weight_trade_unit * $orderItem->quantity;
            }

            foreach (OrderBuilder::getPriceColumns() as $column) {
                if (array_key_exists($column, $allegroOrderItem->price_override)) {
                    $orderItem->$column = $allegroOrderItem->price_override[$column] ?: 0;
                } elseif ($column === "gross_selling_price_commercial_unit") {
                    $orderItem->$column = $price->gross_price_of_packing;
                } else {
                    $orderItem->$column = $price->$column;
                }
            }

            unset($orderItem->type);
            $order->items()->save($orderItem);
        }
        $order->weight = round($weight, 2);
        $order->saveQuietly();
    }

    /**
     * @param Order $order
     * @param array $allegroPayment
     *
     * @return void
     */
    private function savePayments(Order $order, array $allegroPayment)
    {
        OrderPayment::create([
            'declared_sum' => $allegroPayment['paidAmount']['amount'] ?? 0,
            'master_payment_id' => null,
            'order_id' => $order->id,
            'promise' => true,
            'promise_date' => $allegroPayment['finishedAt'],
            'payer' => $order->customer->email,
            'operation_type' => OrderPaymentsEnum::DECLARED_FROM_ALLEGRO,
            'operation_id' => $allegroPayment['id'],
        ]);
        $order->labels()->attach(Label::BOOKED_FIRST_PAYMENT);
    }

    /**
     * Create or update customer address.
     *
     * @param Customer $customer
     * @param array $data
     * @param string $type
     *
     * @return void
     */
    private function createOrUpdateCustomerAddress(Customer $customer, array $data, string $type = CustomerAddress::ADDRESS_TYPE_STANDARD)
    {
        list($street, $flatNo) = $this->getAddress($data['address']['street'] ?? $data['street']);

        $customerAddress = CustomerAddress::firstOrNew([
            'type' => $type,
            'customer_id' => $customer->id,
        ]);

        $country = Country::firstOrCreate(['iso2' => $data['address']['countryCode'] ?? $data['countryCode']], ['name' => $data['address']['countryCode'] ?? $data['countryCode']]);

        $phoneAndCode = Helper::prepareCodeAndPhone($data['phoneNumber'] ?? $data['address']['phoneNumber']);

        $customerAddressData = [
            'type' => $type,
            'firstname' => $data['firstName'] ?? $data['address']['naturalPerson']['firstName'] ?? null,
            'lastname' => $data['lastName'] ?? $data['address']['naturalPerson']['lastName'] ?? null,
            'address' => $street,
            'flat_number' => $flatNo,
            'city' => $data['address']['city'],
            'firmname' => $data['companyName'] ?? $data['address']['company']['name'] ?? null,
            'nip' => $data['company']['taxId'] ?? null,
            'postal_code' => $data['address']['postCode'] ?? $data['address']['zipCode'],
            'phone' => implode('', $phoneAndCode),
            'customer_id' => $customer->id,
            'email' => $customer->login,
            'country_Id' => $country->id,
            'isAbroad' => $country->id != 1
        ];
        $customerAddress->fill($customerAddressData);
        $customerAddress->save();
    }

    private function getAddressOneWord(string $address): array
    {
        $flatNo = "";
        $lettersInARow = 0;

        $addressReverseArray = array_reverse(str_split($address));
        foreach ($addressReverseArray as $i => $character) {
            $characterIsNumeric = is_numeric($character);
            if ($characterIsNumeric || $character == '/') {
                if ($characterIsNumeric && $lettersInARow > 0) {
                    $charactersToAdd = array_slice($addressReverseArray, $i - $lettersInARow, $lettersInARow);
                    $flatNo = StringHelper::addCharactersInReverseOrder($flatNo, $charactersToAdd);
                }

                $lettersInARow = 0;
                $flatNo = $character . $flatNo;
                continue;
            }

            $lettersInARow++;
            if ($lettersInARow >= 3) {
                break;
            }
        }

        $street = trim(substr($address, 0, strlen($address) - strlen($flatNo)));
        $flatNo = trim($flatNo);

        return [$street, $flatNo];
    }

    private function getAddressMultipleWords(string $address): array
    {
        $address = StringHelper::removeMultipleSpaces($address);

        list($street, $flatNo) = StringHelper::separateLastWord($address);

        $rememberString = "";
        $streetReverseArray = array_reverse(explode(" ", $street));
        foreach ($streetReverseArray as $part) {
            if (StringHelper::hasThreeLettersInARow($part)) {
                list($_, $toAddFlatNo) = $this->getAddressOneWord($part);
                if ($rememberString != "") {
                    $toAddFlatNo .= " " . $rememberString;
                }
                $flatNo = $toAddFlatNo . " " . $flatNo;
                break;
            }

            if (StringHelper::isAlpha($part)) {
                if ($rememberString != "") {
                    break;
                }

                $rememberString = $part;
                continue;
            }

            if ($rememberString != "") {
                $part .= " " . $rememberString;
                $rememberString = "";
            }

            $flatNo = $part . " " . $flatNo;
        }

        $street = trim(substr($address, 0, strlen($address) - strlen($flatNo)));
        $flatNo = trim($flatNo);

        return [$street, $flatNo];
    }

    /**
     * @param $address
     *
     * @return array
     */
    private function getAddress(string $address): array
    {
        $toRemove = ['ul.', 'Ul.', 'nr.', 'Nr.'];
        $address = str_replace($toRemove, '', $address);
        $address = trim($address);

        if (str_contains($address, ' ')) {
            return $this->getAddressMultipleWords($address);
        }

        return $this->getAddressOneWord($address);
    }


    /**
     * Create or update order address.
     *
     * @param Order $order
     * @param array $buyer
     * @param array $address
     * @param string $type
     *
     * @return void
     */
    private function createOrUpdateOrderAddress(Order $order, array $buyer, array $address, string $type = OrderAddress::TYPE_DELIVERY): void
    {
        if (isset($address['address'])) {
            $address = array_merge($address, $address['address']);
        }
        if (count($address) === 0) {
            return;
        }
        list($street, $flatNo) = $this->getAddress($address['street']);
        $country = Country::firstOrCreate(['iso2' => $address['countryCode']], ['name' => $address['countryCode']]);

        $orderAddress = OrderAddress::query()->firstOrNew([
            'type' => $type,
            'order_id' => $order->id,
        ]);

        list($code, $phone) = Helper::prepareCodeAndPhone($address['phoneNumber']);

        if (isset($address['naturalPerson'])) {
            $address['firstName'] = $address['naturalPerson']['firstName'];
            $address['lastName'] = $address['naturalPerson']['lastName'];
        }

        if (empty($address['city']) && !empty($street)) {
            $address['city'] = $street;
        }

        if (empty($street) && !empty($address['city'])) {
            $street = $address['city'];
        }

        $addressData = [
            'type' => $type,
            'firstname' => $address['firstName'] ?? null,
            'lastname' => $address['lastName'] ?? null,
            'address' => $street,
            'flat_number' => $flatNo,
            'city' => $address['city'],
            'firmname' => (!empty($address['company']['taxId'])) ? ($address['company']['name'] ?? $address['companyName'] ?? null) : null,
            'nip' => $address['company']['taxId'] ?? null,
            'postal_code' => $address['zipCode'] ?? $address['postCode'],
            'phone_code' => $code,
            'phone' => $phone,
            'order_id' => $order->id,
            'email' => $buyer['email'],
            'country_Id' => $country->id,
            'isAbroad' => $country->id != 1
        ];
        $orderAddress->fill($addressData);

        if (
            ($type === OrderAddress::TYPE_INVOICE && empty($address['company']['taxId'])) &&
            (empty($orderAddress->firstName) || empty($orderAddress->lastName) || empty($orderAddress->address))
        ) {
            $deliveryAddress = $order->getDeliveryAddress();

            if (!empty($deliveryAddress)) {
                $orderAddress->fill([
                    'firstname' => $deliveryAddress->firstname,
                    'lastname' => $deliveryAddress->lastname,
                    'address' => $deliveryAddress->address,
                    'flat_number' => $deliveryAddress->flat_number,
                    'city' => $deliveryAddress->city,
                    'postal_code' => $deliveryAddress->postal_code,
                    'phone' => $deliveryAddress->phone,
                    'phone_code' => $deliveryAddress->phone_code,
                ]);
            }
        }

        $orderAddress->save();
    }

    /**
     * @param array $allegroDelivery
     *
     * @return void
     */
    private function addOrderPackage($order, $allegroDelivery)
    {
        $deliveryMethod = $allegroDelivery['method']['id'];
        if (!($packageTemplate = PackageTemplate::AllegroDeliveryMethod($deliveryMethod)->first())) {
            Log::info(
                'Order: ' . $order->id .
                '\r\nPackage template not found: ' . $deliveryMethod .
                '\r\nAllegro delivery data: ' . json_encode($allegroDelivery ?? [])
            );
            return null;
        }

        $packageNumber = OrderPackage::where('order_id', $order->id)->max('number');
        $totalPackages = $allegroDelivery['calculatedNumberOfPackages'];
        while ($allegroDelivery['calculatedNumberOfPackages']) {
            $packageNumber++;

            $package = $this->createPackage($packageTemplate, $order, $packageNumber);
            foreach ($order->items as $item) {
                $product = $item->product;
                $package->packedProducts()->attach($product->id, ['quantity' => $item->quantity / $totalPackages]);
            }

            $allegroDelivery['calculatedNumberOfPackages']--;
        }
    }

    /**
     * @param $packTemplate
     * @param $order
     * @param $packageNumber
     *
     * @return OrderPackage
     */
    public function createPackage($packTemplate, $order, $packageNumber)
    {
        $orderId = $order->id;
        $pack = new OrderPackage();
        $pack->order_id = $orderId;
        $pack->size_a = $packTemplate->sizeA;
        $pack->size_b = $packTemplate->sizeB;
        $pack->size_c = $packTemplate->sizeC;
        $pack->delivery_courier_name = $packTemplate->delivery_courier_name;
        $pack->service_courier_name = $packTemplate->service_courier_name;
        $pack->weight = $packTemplate->weight;
        $pack->number = $packageNumber;
        $pack->chosen_data_template = $packTemplate->name;
        $pack->cost_for_client = $packTemplate->approx_cost_client;
        $pack->cost_for_company = $packTemplate->approx_cost_firm;
        $pack->content = $packTemplate->content ?? '';
        $pack->notices = $orderId . '/' . $packageNumber;
        $pack->symbol = $packTemplate->symbol;
        if (!file_exists(storage_path('app/public/protocols/day-close-protocol-' . $packTemplate->delivery_courier_name . '-' . Carbon::today()->toDateString() . '.pdf'))) {
            $date = Carbon::today();
        } else {
            $date = Carbon::today()->addWeekday();
        }
//        else if ($packTemplate->accept_time) {
//            $date = $helper->calculateShipmentDate($packTemplate->accept_time, $packTemplate->accept_time);
//        } else {
//            $date = $helper->calculateShipmentDate(9, 9);
//        }
        $pack->shipment_date = $date;
        $pack->cost_for_client = $packTemplate->approx_cost_client;
        $pack->quantity = 1;
        $pack->status = 'NEW';
        $pack->container_type = $packTemplate->container_type;
        $pack->packing_type = $packTemplate->packing_type;
        $pack->shape = $packTemplate->shape;
        $pack->save();
        return $pack;
    }

    /**
     * @return void
     */
    private function generateTask(): void
    {
        $date = Carbon::now();
        $taskPrimal = Task::create([
            'warehouse_id' => Warehouse::OLAWA_WAREHOUSE_ID,
            'user_id' => User::OLAWA_USER_ID,
            'created_by' => 1,
            'name' => 'Grupa zadań - ' . $date->format('d-m'),
            'color' => Task::DEFAULT_COLOR,
            'status' => Task::WAITING_FOR_ACCEPT
        ]);
        TaskSalaryDetails::create([
            'task_id' => $taskPrimal->id,
            'consultant_value' => 0,
            'warehouse_value' => 0
        ]);
    }
}
