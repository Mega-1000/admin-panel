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
use App\Mail\SendLog;

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
        return $this->oldStore($request->all());
    }

    public function new(StoreOrderRequest $request)
    {
        $data = $request->all();
        foreach ($data['order_items'] as $k => $order) {
            if (is_array($order)) {
                continue;
            }
            $data['order_items'][$k] = json_decode($order, true);
        }
        if (isset($data['customer_login'])) {
            $customer = $this->customerRepository->findByField('login', $data['customer_login'])->first();
            if (empty($customer)) {
                $this->customerRepository->create([
                    'login' => $data['customer_login'],
                    'email' => $data['customer_login'],
                    'password' => bcrypt($data['phone']),
                ]);
            }
        }
        $data['old_prices'] = 1;
        $data['old_prices'] = 1;
        return $this->newStore($data);
    }

    private function newStore($data)
    {
        DB::beginTransaction();
        try {
            if (isset($data['id'])) {
                $id = $data['id'];
            } else {
                $id = null;
            }
            $orderExists = $this->orderRepository->findByField('id', $id);
            if (isset($data['customer_login'])) {
                $customer = $this->customerRepository->findByField('login', $data['customer_login'])->first();

                if (empty($customer)) {
                    $customer = $this->customerRepository->create([
                        'login' => $data['customer_login'],
                        'email' => $data['customer_login'],
                        'password' => 'new_client_from_old_db',
                    ]);
                    $this->customerAddressRepository->create([
                        'type' => 'DELIVERY_ADDRESS',
                        'customer_id' => $customer->id,
                        'email' => $data['customer_login'],
                    ]);
                } else {
                    $customer = $this->customerRepository->findByField('login', $data['customer_login'])->first();
                }
                $data['customer_id'] = $customer->id;
            } else {
                if (count($orderExists) == 0) {
                    throw new \Exception('Nie podano customer_login, pomimo że zamówienie nie istnieje.');
                }
            }

            if (count($orderExists) == 0) {
                $data['status_id'] = 1;
                $orderCustomerOpenExists = $this->orderRepository->findWhere(
                    [
                        ['customer_id', '=', $customer->id],
                        ['status_id', '<>', 6],
                        ['status_id', '<>', 8],
                        ['employee_id', '<>', null],
                    ]
                )->first();

                if (!empty($orderCustomerOpenExists)) {
                    $data['employee_id'] = $orderCustomerOpenExists->employee_id;
                }
            }
            $order = $this->orderRepository->updateOrCreate(['id' => -1], $data);
            $orderTotal = 0;
            $weight = 0;
            $orderItems = $this->orderItemRepository->findWhere(['order_id' => $order->id]);
            if (isset($data['old_id_from_front_db']) && !empty($data['old_id_from_front_db'])) {
                $order2 = $this->orderRepository->findWhere(['id_from_front_db' => $data['old_id_from_front_db']])->first();
                $orderItems = $this->orderItemRepository->findWhere(['order_id' => $order2->id]);
            }
            $oldPrices = [];
            $prices = [];

            foreach ($orderItems as $item) {
                $oldPrices[$item->product_id]['net_purchase_price_commercial_unit'] = $item->net_purchase_price_commercial_unit;
                $oldPrices[$item->product_id]['net_purchase_price_basic_unit'] = $item->net_purchase_price_basic_unit;
                $oldPrices[$item->product_id]['net_purchase_price_calculated_unit'] = $item->net_purchase_price_calculated_unit;
                $oldPrices[$item->product_id]['net_purchase_price_aggregate_unit'] = $item->net_purchase_price_aggregate_unit;
                $oldPrices[$item->product_id]['net_purchase_price_the_largest_unit'] = $item->net_purchase_price_the_largest_unit;
                $oldPrices[$item->product_id]['net_selling_price_commercial_unit'] = $item->net_selling_price_commercial_unit;
                $oldPrices[$item->product_id]['net_selling_price_basic_unit'] = $item->net_selling_price_basic_unit;
                $oldPrices[$item->product_id]['net_selling_price_calculated_unit'] = $item->net_selling_price_calculated_unit;
                $oldPrices[$item->product_id]['net_selling_price_aggregate_unit'] = $item->net_selling_price_aggregate_unit;
            }


            foreach ($orderItems as $item) {
                $item->delete();
            }


            if (isset($data['order_items']) && count($data['order_items']) > 0) {
                foreach ($data['order_items'] as $item) {
                    $product = $this->productRepository->findByField('symbol', $item['product_symbol'])->first();
                    if (empty($product)) {
                        return $this->notFoundResponse("Not found provided Product Symbol: " . $item['product_symbol']);
                    }
                    $productPrice = $this->productPriceRepository->findByField('product_id', $product->id)->first();
                    if (isset($data['old_prices']) && $data['old_prices'] == 1 && isset($oldPrices[$product->id])) {
                        $prices['net_purchase_price_commercial_unit'] = $oldPrices[$product->id]['net_purchase_price_commercial_unit'];
                        $prices['net_purchase_price_basic_unit'] = $oldPrices[$product->id]['net_purchase_price_basic_unit'];
                        $prices['net_purchase_price_calculated_unit'] = $oldPrices[$product->id]['net_purchase_price_calculated_unit'];
                        $prices['net_purchase_price_aggregate_unit'] = $oldPrices[$product->id]['net_purchase_price_aggregate_unit'];
                        $prices['net_purchase_price_the_largest_unit'] = $oldPrices[$product->id]['net_purchase_price_the_largest_unit'];
                        $prices['net_selling_price_basic_unit'] = $oldPrices[$product->id]['net_selling_price_basic_unit'];
                        $prices['net_selling_price_commercial_unit'] = $oldPrices[$product->id]['net_selling_price_commercial_unit'];
                        $prices['net_selling_price_calculated_unit'] = $oldPrices[$product->id]['net_selling_price_calculated_unit'];
                        $prices['net_selling_price_aggregate_unit'] = $oldPrices[$product->id]['net_selling_price_aggregate_unit'];
                        $orderTotal += $oldPrices[$product->id]['net_selling_price_commercial_unit'] * $item['quantity'];
                    } else {
                        $prices['net_purchase_price_commercial_unit'] = $productPrice->net_purchase_price_commercial_unit_after_discounts;
                        $prices['net_purchase_price_basic_unit'] = $productPrice->net_purchase_price_basic_unit_after_discounts;
                        $prices['net_purchase_price_calculated_unit'] = $productPrice->net_purchase_price_calculated_unit_after_discounts;
                        $prices['net_purchase_price_aggregate_unit'] = $productPrice->net_purchase_price_aggregate_unit_after_discounts;
                        $prices['net_purchase_price_the_largest_unit'] = $productPrice->net_purchase_price_the_largest_unit_after_discounts;
                        $prices['net_selling_price_basic_unit'] = $productPrice->net_selling_price_basic_unit;
                        $prices['net_selling_price_commercial_unit'] = $productPrice->net_selling_price_commercial_unit;
                        $prices['net_selling_price_calculated_unit'] = $productPrice->net_selling_price_calculated_unit;
                        $prices['net_selling_price_aggregate_unit'] = $productPrice->net_selling_price_aggregate_unit;
                        $orderTotal += $productPrice->net_selling_price_commercial_unit * $item['quantity'];
                    }

                    $this->orderItemRepository->create(
                        array_merge(
                            [
                                'order_id' => $order->id,
                                'product_id' => $product->id,
                            ],
                            $item,
                            $prices
                        )
                    );
                    if (!empty($product->weight_trade_unit)) {
                        $weight += $product->weight_trade_unit * $item['quantity'];
                    }
                }
            }

            $order->total_price = $orderTotal * 1.23;
            $order->weight = $weight;
            $order->save();

            if ($data['rewrite'] == 0) {
                if (isset($data['delivery_address']) && !empty($data['delivery_address'])) {
                    $this->orderAddressRepository->updateOrCreate(
                        [
                            'order_id' => $order->id,
                            'type' => 'DELIVERY_ADDRESS',
                        ],
                        array_merge(
                            [
                                'order_id' => $order->id,
                                'type' => 'DELIVERY_ADDRESS',
                            ],
                            $data['delivery_address']
                        )
                    );
                }

                if (isset($data['invoice_address']) && !empty($data['invoice_address'])) {
                    $this->orderAddressRepository->updateOrCreate(
                        [
                            'order_id' => $order->id,
                            'type' => 'INVOICE_ADDRESS',
                        ],
                        array_merge(
                            [
                                'order_id' => $order->id,
                                'type' => 'INVOICE_ADDRESS',
                            ],
                            $data['invoice_address']
                        )
                    );
                }
            }
            DB::commit();
            return $this->createdResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Problem with create new order :' . $e->getMessage(),
                ['request' => $data, 'class' => get_class($this), 'line' => __LINE__]
            );
            $message = 'Wystąpił wewnętrzny błąd systemu przy składaniu zamówienia. Dział techniczny został o tym poinformowany.';
            return $this->createdErrorResponse($message);
        }
    }

    //todo: remove duplicates after 2nd company finishes their work
    private function oldStore($data)
    {
        DB::beginTransaction();
        try {
            if (isset($data['id_from_front_db'])) {
                $orderFrontId = $data['id_from_front_db'];
            } else {
                $orderFrontId = -1;
            }
            $orderExists = $this->orderRepository->findByField('id_from_front_db', $orderFrontId);
            if (isset($data['customer_login'])) {
                $customer = $this->customerRepository->findByField('login', $data['customer_login'])->first();

                if (empty($customer)) {
                    $customer = $this->customerRepository->create([
                        'login' => $data['customer_login'],
                        'email' => $data['customer_login'],
                        'password' => 'new_client_from_old_db',
                    ]);
                    $this->customerAddressRepository->create([
                        'type' => 'DELIVERY_ADDRESS',
                        'customer_id' => $customer->id,
                        'email' => $data['customer_login'],
                    ]);
                } else {
                    $customer = $this->customerRepository->findByField('login', $data['customer_login'])->first();
                }
                $data['customer_id'] = $customer->id;
            } else {
                if (count($orderExists) == 0) {
                    throw new \Exception('Nie podano customer_login, pomimo że zamówienie nie istnieje.');
                }
            }


            if (count($orderExists) == 0) {
                $data['status_id'] = 1;
                $orderCustomerOpenExists = $this->orderRepository->findWhere(
                    [
                        ['customer_id', '=', $customer->id],
                        ['status_id', '<>', 6],
                        ['status_id', '<>', 8],
                        ['employee_id', '<>', null],
                    ]
                )->first();

                if (!empty($orderCustomerOpenExists)) {
                    $data['employee_id'] = $orderCustomerOpenExists->employee_id;
                }
            }

            $order = $this->orderRepository->updateOrCreate(['id_from_front_db' => $data['id_from_front_db']], $data);
            $orderTotal = 0;
            $weight = 0;
            $orderItems = $this->orderItemRepository->findWhere(['order_id' => $order->id]);
            if (isset($data['old_id_from_front_db']) && !empty($data['old_id_from_front_db'])) {
                $order2 = $this->orderRepository->findWhere(['id_from_front_db' => $data['old_id_from_front_db']])->first();
                $orderItems = $this->orderItemRepository->findWhere(['order_id' => $order2->id]);
            }
            $oldPrices = [];
            $prices = [];

            foreach ($orderItems as $item) {
                $oldPrices[$item->product_id]['net_purchase_price_commercial_unit'] = $item->net_purchase_price_commercial_unit;
                $oldPrices[$item->product_id]['net_purchase_price_basic_unit'] = $item->net_purchase_price_basic_unit;
                $oldPrices[$item->product_id]['net_purchase_price_calculated_unit'] = $item->net_purchase_price_calculated_unit;
                $oldPrices[$item->product_id]['net_purchase_price_aggregate_unit'] = $item->net_purchase_price_aggregate_unit;
                $oldPrices[$item->product_id]['net_purchase_price_the_largest_unit'] = $item->net_purchase_price_the_largest_unit;
                $oldPrices[$item->product_id]['net_selling_price_commercial_unit'] = $item->net_selling_price_commercial_unit;
                $oldPrices[$item->product_id]['net_selling_price_basic_unit'] = $item->net_selling_price_basic_unit;
                $oldPrices[$item->product_id]['net_selling_price_calculated_unit'] = $item->net_selling_price_calculated_unit;
                $oldPrices[$item->product_id]['net_selling_price_aggregate_unit'] = $item->net_selling_price_aggregate_unit;
            }


            foreach ($orderItems as $item) {
                $item->delete();
            }


            if (isset($data['order_items']) && count($data['order_items']) > 0) {
                foreach ($data['order_items'] as $item) {
                    $product = $this->productRepository->findByField('symbol', $item['product_symbol'])->first();
                    if (empty($product)) {
                        return $this->notFoundResponse("Not found provided Product Symbol: " . $item['product_symbol']);
                    }
                    $productPrice = $this->productPriceRepository->findByField('product_id', $product->id)->first();
                    if (isset($data['old_prices']) && $data['old_prices'] == 1 && isset($oldPrices[$product->id])) {
                        $prices['net_purchase_price_commercial_unit'] = $oldPrices[$product->id]['net_purchase_price_commercial_unit'];
                        $prices['net_purchase_price_basic_unit'] = $oldPrices[$product->id]['net_purchase_price_basic_unit'];
                        $prices['net_purchase_price_calculated_unit'] = $oldPrices[$product->id]['net_purchase_price_calculated_unit'];
                        $prices['net_purchase_price_aggregate_unit'] = $oldPrices[$product->id]['net_purchase_price_aggregate_unit'];
                        $prices['net_purchase_price_the_largest_unit'] = $oldPrices[$product->id]['net_purchase_price_the_largest_unit'];
                        $prices['net_selling_price_basic_unit'] = $oldPrices[$product->id]['net_selling_price_basic_unit'];
                        $prices['net_selling_price_commercial_unit'] = $oldPrices[$product->id]['net_selling_price_commercial_unit'];
                        $prices['net_selling_price_calculated_unit'] = $oldPrices[$product->id]['net_selling_price_calculated_unit'];
                        $prices['net_selling_price_aggregate_unit'] = $oldPrices[$product->id]['net_selling_price_aggregate_unit'];
                        $orderTotal += $oldPrices[$product->id]['net_selling_price_commercial_unit'] * $item['quantity'];
                    } else {
                        $prices['net_purchase_price_commercial_unit'] = $productPrice->net_purchase_price_commercial_unit_after_discounts;
                        $prices['net_purchase_price_basic_unit'] = $productPrice->net_purchase_price_basic_unit_after_discounts;
                        $prices['net_purchase_price_calculated_unit'] = $productPrice->net_purchase_price_calculated_unit_after_discounts;
                        $prices['net_purchase_price_aggregate_unit'] = $productPrice->net_purchase_price_aggregate_unit_after_discounts;
                        $prices['net_purchase_price_the_largest_unit'] = $productPrice->net_purchase_price_the_largest_unit_after_discounts;
                        $prices['net_selling_price_basic_unit'] = $productPrice->net_selling_price_basic_unit;
                        $prices['net_selling_price_commercial_unit'] = $productPrice->net_selling_price_commercial_unit;
                        $prices['net_selling_price_calculated_unit'] = $productPrice->net_selling_price_calculated_unit;
                        $prices['net_selling_price_aggregate_unit'] = $productPrice->net_selling_price_aggregate_unit;
                        $orderTotal += $productPrice->net_selling_price_commercial_unit * $item['quantity'];
                    }

                    $this->orderItemRepository->create(
                        array_merge(
                            [
                                'order_id' => $order->id,
                                'product_id' => $product->id,
                            ],
                            $item,
                            $prices
                        )
                    );
                    if (!empty($product->weight_trade_unit)) {
                        $weight += $product->weight_trade_unit * $item['quantity'];
                    }
                }
            }

            $order->total_price = $orderTotal * 1.23;
            $order->weight = $weight;
            $order->save();

            if ($data['rewrite'] == 0) {
                if (isset($data['delivery_address']) && !empty($data['delivery_address'])) {
                    $this->orderAddressRepository->updateOrCreate(
                        [
                            'order_id' => $order->id,
                            'type' => 'DELIVERY_ADDRESS',
                        ],
                        array_merge(
                            [
                                'order_id' => $order->id,
                                'type' => 'DELIVERY_ADDRESS',
                            ],
                            $data['delivery_address']
                        )
                    );
                }

                if (isset($data['invoice_address']) && !empty($data['invoice_address'])) {
                    $this->orderAddressRepository->updateOrCreate(
                        [
                            'order_id' => $order->id,
                            'type' => 'INVOICE_ADDRESS',
                        ],
                        array_merge(
                            [
                                'order_id' => $order->id,
                                'type' => 'INVOICE_ADDRESS',
                            ],
                            $data['invoice_address']
                        )
                    );
                }
            }
            DB::commit();
            return $this->createdResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Problem with create new order :' . $e->getMessage(),
                ['request' => $data, 'class' => get_class($this), 'line' => __LINE__]
            );
            $message = 'Wystąpił wewnętrzny błąd systemu przy składaniu zamówienia. Dział techniczny został o tym poinformowany.';
            return $this->createdErrorResponse($message);
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
}
