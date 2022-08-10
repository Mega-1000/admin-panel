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
use App\Helpers\Helper;
use App\Helpers\LabelsHelper;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPackagesDataHelper;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\AllegroOrderService;
use App\Services\OrderAddressService;
use App\Services\ProductService;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var AllegroOrderService
     */
    private $allegroOrderService;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var OrderPackagesDataHelper
     */
    private $orderPackagesDataHelper;

    /**
     * @var float
     */
    private $tax;

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $this->customerRepository = app(CustomerRepository::class);
        $this->allegroOrderService = app(AllegroOrderService::class);
        $this->productRepository = app(ProductRepository::class);
        $this->productService = app(ProductService::class);
        $this->orderRepository = app(OrderRepository::class);
        $this->orderPackagesDataHelper = app(OrderPackagesDataHelper::class);

        $this->tax = (float)(1 + env('VAT'));

        $allegroOrders = $this->allegroOrderService->getPendingOrders();
        foreach ($allegroOrders as $allegroOrder) {
            try {
                $orderModel = AllegroOrder::firstOrNew(['order_id' => $allegroOrder['id']]);
                $orderModel->order_id = $allegroOrder['id'];
                $orderModel->buyer_email = $allegroOrder['buyer']['email'];
                $orderModel->save();

                if (Order::where('allegro_form_id', $allegroOrder['id'])->count() > 0) {
                    continue;
                }

                $order = new Order();
                $customer = $this->findOrCreateCustomer($allegroOrder['buyer'], $allegroOrder['delivery']['address']);
                $order->customer_id = $customer->id;
                $order->allegro_form_id = $allegroOrder['id'];
                $order->status_id = 1;
                $order->allegro_operation_date = $allegroOrder['lineItems'][0]['boughtAt'];
                $order->allegro_additional_service = $allegroOrder['delivery']['method']['name'];
                $order->payment_channel = $allegroOrder['payment']['provider'];
                $order->allegro_payment_id = $allegroOrder['payment']['id'];
                $order->saveQuietly();

                if ($allegroOrder['messageToSeller'] !== null) {
                    $this->createChat($order->id, $customer->id, $allegroOrder['messageToSeller']);
                    $order->labels()->attach(MessagesHelper::MESSAGE_YELLOW_LABEL_ID);
                }

                list($orderItems, $undefinedProductSymbol) = $this->mapItems($allegroOrder['lineItems']);

                if ($undefinedProductSymbol) {
                    $order->consultant_notices = 'Nie znaleziono produktu o symbolu ' . $undefinedProductSymbol['id'];
                    dispatch_now(new AddLabelJob($order, [Label::WAREHOUSE_MARK]));
                }

                $this->saveOrderItems($orderItems, $order);

                $this->savePayments($order, $allegroOrder['payment']);

                $invoiceAddress = $allegroOrder['invoice']['address'] ?? $allegroOrder['buyer'];
                if (empty($invoiceAddress['phoneNumber'])) {
                    $invoiceAddress['phoneNumber'] = $allegroOrder['buyer']['phoneNumber'] ?? $allegroOrder['delivery']['address']['phoneNumber'];
                }

                if (empty($allegroOrder['buyer']['address']['phoneNumber'])) {
                    $allegroOrder['buyer']['address']['phoneNumber'] = $invoiceAddress['phoneNumber'];
                }

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
                $this->createOrUpdateOrderAddress($order, $allegroOrder['buyer'], $allegroOrder['delivery']['address']);

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

                if (!Helper::phoneIsCorrect($orderDeliveryAddress->phone)) {
                    $order->labels_log .= Order::formatMessage(null, 'ebudownictwo@wp.pl 691801594 55-200');
                    $order->labels()->attach(176);
                    $order->saveQuietly();
                }

                // order package
                $this->addOrderPackage($order, $allegroOrder['delivery']);
                $order->shipment_price_for_client = $allegroOrder['delivery']['cost']['amount'];

                $order->total_price = $allegroOrder['summary']['totalToPay']['amount'];
                $firmSource = FirmSource::byFirmAndSource(env('FIRM_ID'), 1)->first();
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
                        $order->labels()->attach(133);
                        $order->labels()->attach(Label::BLUE_HAMMER_ID);
                        $order->labels()->attach(69);
                    }
                }

                dispatch_now(new AddLabelJob($order, [177]));
//                $this->allegroOrderService->setSellerOrderStatus($allegroOrder['id'], AllegroOrderService::STATUS_PROCESSING);
            } catch (Throwable $ex) {
                Log::error($ex->getMessage(), [
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'orderId' => (isset($order)) ? $order->id : $allegroOrder['id']
                ]);
            }
        }
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
            'amount' => $allegroPayment['paidAmount']['amount'],
            'master_payment_id' => null,
            'order_id' => $order->id,
            'promise' => true,
            'promise_date' => $allegroPayment['finishedAt'],
        ]);
        $order->labels()->attach(Label::BOOKED_FIRST_PAYMENT);
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
        $helper = new OrderPackagesDataHelper();

        if (file_exists(storage_path('app/public/protocols/day-close-protocol-' . $packTemplate->delivery_courier_name . '-' . Carbon::today()->toDateString() . '.pdf'))) {
            $date = Carbon::today()->addWeekday();
        } else if ($packTemplate->accept_time) {
            $date = $helper->calculateShipmentDate($packTemplate->accept_time, $packTemplate->accept_time);
        } else {
            $date = $helper->calculateShipmentDate(9, 9);
        }
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

                    if (strpos(end($symbol), 'Q') !== false) {
                        $quantity = (int)(explode('Q', end($symbol))[1]);
                    } elseif (strpos(end($symbol), 'Y') !== false) {
                        $quantity = (int)(explode('Y', end($symbol))[1]);
                    } elseif (strpos(end($symbol), 'Z') !== false) {
                        $quantity = (int)(explode('Z', end($symbol))[1]);
                    } elseif (strpos(end($symbol), 'N') !== false) {
                        $quantity = (int)(explode('N', end($symbol))[1]);
                    } else {
                        $quantity = false;
                    }

                    $newSymbol = [$symbol[0], $symbol[1], '0'];
                    $newSymbol = join('-', $newSymbol);

                    $product = $this->productRepository->findWhere(['symbol' => $newSymbol])->first();
                }
                if (empty($product)) {
                    $product = Product::getDefaultProduct();
                    $undefinedProductSymbol = $item['offer']['external'];
                }

                if (!$product->stock()->count()) {
                    ProductStock::create([
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
     * Find customer by buyer
     *
     * @param array $buyer
     * @param array $deliveryAddress
     *
     * @return Customer
     * @throws Exception
     */
    private function findOrCreateCustomer(array $buyer, array $deliveryAddress = []): Customer
    {
        $buyerEmail = $buyer['email'];

        if (preg_match('/\+([a-zA-Z0-9]+)@/', $buyer['email'], $matches)) {
            $buyerEmail = str_replace('+' . $matches[1], '', $buyer['email']);
        }

        $customer = $this->customerRepository->findWhere(['login' => $buyerEmail])->first();
        if (empty($buyer['phoneNumber']) || $buyer['phoneNumber'] == 'brak') {
            $buyer['phoneNumber'] = $deliveryAddress['phoneNumber'];
        }
        $customerPhone = str_replace('+48', '', $buyer['phoneNumber']);
        if ($customer === null) {
            $customer = new Customer();
            $customer->login = $buyerEmail;
            $customer->nick_allegro = $buyer['login'];
            $customer->password = $customer->generatePassword($customerPhone);
            $customer->save();
        } else {
            if (!Hash::check($customerPhone, $customer->password)) {
                $customer->password = $customer->generatePassword($customerPhone);
                $customer->save();
            }
        }

        return $customer;
    }

    /**
     * Create chat
     *
     * @param int    $orderId
     * @param int    $customerId
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
     * Create or update order address.
     *
     * @param Order  $order
     * @param array  $buyer
     * @param array  $address
     * @param string $type
     *
     * @return void
     */
    private function createOrUpdateOrderAddress(Order $order, array $buyer, array $address, string $type = OrderAddress::TYPE_DELIVERY): void
    {
        if (isset($address['address'])) {
            $address = array_merge($address, $address['address']);
        }
        list($street, $flatNo) = $this->getAddress($address['street']);
        $country = Country::firstOrCreate(['iso2' => $address['countryCode']], ['name' => $address['countryCode']]);

        $orderAddress = OrderAddress::firstOrNew([
            'type' => $type,
            'order_id' => $order->id,
        ]);

        list($code, $phone) = Helper::prepareCodeAndPhone($address['phoneNumber']);

        $addressData = [
            'type' => $type,
            'firstname' => $address['firstName'] ?? $address['naturalPerson']['firstName'],
            'lastname' => $address['lastName'] ?? $address['naturalPerson']['lastName'],
            'address' => $street,
            'flat_number' => $flatNo,
            'city' => $address['city'],
            'firmname' => $address['companyName'] ?? $address['company']['name'] ?? null,
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
        $orderAddress->save();
    }

    /**
     * Create or update customer address.
     *
     * @param Customer $customer
     * @param array    $data
     * @param string   $type
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

        $phoneAndCode = Helper::prepareCodeAndPhone($data['phoneNumber']);
        $customerAddressData = [
            'type' => $type,
            'firstname' => $data['firstName'] ?? $data['naturalPerson']['firstName'] ?? null,
            'lastname' => $data['lastName'] ?? $data['naturalPerson']['lastName'] ?? null,
            'address' => $street,
            'flat_number' => $flatNo,
            'city' => $data['address']['city'] ?? $data['city'],
            'firmname' => $data['companyName'] ?? $data['company']['name'] ?? null,
            'nip' => $data['company']['taxId'] ?? null,
            'postal_code' => $data['address']['postCode'] ?? $data['zipCode'],
            'phone' => implode('', $phoneAndCode),
            'customer_id' => $customer->id,
            'email' => $customer->login
        ];
        $customerAddress->fill($customerAddressData);
        $customerAddress->save();
    }

    /**
     * @param $address
     *
     * @return array
     */
    private function getAddress($address): array
    {
        $addressArray = explode(' ', $address);
        $lastKey = array_key_last($addressArray);
        $flatNo = $addressArray[$lastKey];
        unset($addressArray[$lastKey]);
        $street = implode(' ', $addressArray);
        return [$street, $flatNo];
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

    /**
     * @param Order $order
     *
     * @return void
     */
    private function addLabels(Order $order): void
    {
        $preventionArray = [];
        dispatch_now(new RemoveLabelJob($order, [LabelsHelper::FINISH_LOGISTIC_LABEL_ID], $preventionArray, LabelsHelper::TRANSPORT_SPEDITION_INIT_LABEL_ID));
        dispatch_now(new RemoveLabelJob($order, [LabelsHelper::TRANSPORT_SPEDITION_INIT_LABEL_ID], $preventionArray, []));
        dispatch_now(new RemoveLabelJob($order, [LabelsHelper::WAIT_FOR_SPEDITION_FOR_ACCEPT_LABEL_ID], $preventionArray, []));
        if ($order->warehouse->id == Warehouse::OLAWA_WAREHOUSE_ID) {
            dispatch_now(new RemoveLabelJob($order, [LabelsHelper::VALIDATE_ORDER], $preventionArray, [LabelsHelper::WAIT_FOR_WAREHOUSE_TO_ACCEPT]));
            $order->createNewTask(5);
        } else {
            dispatch_now(new RemoveLabelJob($order, [LabelsHelper::VALIDATE_ORDER], $preventionArray, [LabelsHelper::SEND_TO_WAREHOUSE_FOR_VALIDATION]));
        }
    }
}
