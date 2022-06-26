<?php

namespace App\Jobs;

use App\Entities\Customer;
use App\Entities\CustomerAddress;
use App\Entities\FirmSource;
use App\Entities\Order;
use App\Entities\OrderAddress;
use App\Entities\OrderItem;
use App\Entities\Product;
use App\Entities\Warehouse;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderBuilder;
use App\Helpers\PackageDivider;
use App\Repositories\CustomerRepository;
use App\Repositories\ProductRepository;
use App\Services\AllegroOrderService;
use App\Services\ProductService;
use App\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Allegro order synchro
 */
class AllegroOrderSynchro implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * @var float
     */
    private $tax;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->customerRepository = app(CustomerRepository::class);
        $this->allegroOrderService = app(AllegroOrderService::class);
        $this->productRepository = app(ProductRepository::class);
        $this->productService = app(ProductService::class);
        $this->tax = (float)(1 + env('VAT'));
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $allegroOrders = $this->allegroOrderService->getPendingOrders()['checkoutForms'];
        $allegroOrders = [$allegroOrders[2]];

        foreach ($allegroOrders as $allegroOrder) {
            $order = new Order();
            $customer = $this->findOrCreateCustomer($allegroOrder['buyer']);
            $order->customer_id = $customer->id;
            $order->status_id = 1;
            $order->save();

            if ($allegroOrder['messageToSeller'] !== null) {
                $this->createChat($order->id, $customer->id, $allegroOrder['messageToSeller']);
                $order->labels()->attach(MessagesHelper::MESSAGE_YELLOW_LABEL_ID);
            }
            $orderItems = $this->mapItems($allegroOrder['lineItems']);
            $this->saveOrderItems($orderItems, $order);

            $this->createOrUpdateOrderAddress($order, $allegroOrder['delivery']['address']);
            $this->createOrUpdateOrderAddress($order, $allegroOrder['invoice']['address'] ?? $allegroOrder['delivery']['address'], OrderAddress::TYPE_INVOICE);
            $this->createOrUpdateCustomerAddress($customer, $allegroOrder['buyer']);
            $this->createOrUpdateCustomerAddress($customer, $allegroOrder['invoice']['address'] ?? $allegroOrder['buyer'], CustomerAddress::ADDRESS_TYPE_INVOICE);

            $packageDivider = new PackageDivider();
            $packageDivider->setItems(new Collection($orderItems));
            $packageDivider->divide();
//            try {
//                $canPay = $this->packageGenerator->divide($data['order_items'], $order);
//            } catch (Exception $exception) {
//                $message = $exception->getMessage();
//                Log::error("Problem with package dividing: $message", ['class' => $exception->getFile(), 'line' => $exception->getLine()]);
//            }
//            if ($this->totalTransportSumCalculator) {
//                $order->shipment_price_for_client = $this->totalTransportSumCalculator->getSum($order);
//            }
//            $order->save();

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
//            $order->setDefaultDates();

            $order->save();
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
        } catch (\Throwable $ex) {
            dd($ex, $item, $symbol);
        }
        return $products;
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
                if ($column === "gross_selling_price_commercial_unit") {
                    $orderItem->$column = $price->gross_price_of_packing;
                } else {
                    $orderItem->$column = $price->$column;
                }
            }
            unset($orderItem->type);
            $order->items()->save($orderItem);
        }
        $order->weight = round($weight, 2);
        $order->save();
    }

    /**
     * Find customer by buyer
     *
     * @param array $buyer
     *
     * @return Customer
     * @throws Exception
     */
    private function findOrCreateCustomer(array $buyer): Customer
    {
        $customer = $this->customerRepository->findWhere(['login' => $buyer['email']])->first();
        $customerPhone = str_replace('+48', '', $buyer['phoneNumber']);
        if ($customer === null) {
            $customer = new Customer();
            $customer->login = $buyer['email'];
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
        } catch (\Throwable $ex) {
            Log::error($ex->getMessage());
        }
    }

    /**
     * Create or update order address.
     *
     * @param Order  $order
     * @param array  $address
     * @param string $type
     *
     * @return void
     */
    private function createOrUpdateOrderAddress(Order $order, array $address, string $type = OrderAddress::TYPE_DELIVERY)
    {
        list($street, $flatNo) = explode(' ', $address['street'], 2);
        $customer = $order->customer;
        OrderAddress::query()->firstOrCreate(
            [
                'type' => $type,
                'firstname' => $address['firstName'] ?? $address['naturalPerson']['firstName'],
                'lastname' => $address['lastName'] ?? $address['naturalPerson']['lastName'],
                'address' => $street,
                'flat_number' => $flatNo,
                'city' => $address['city'],
                'firmname' => $address['companyName'] ?? $address['company']['name'] ?? null,
                'nip' => $address['company']['taxId'] ?? null,
                'postal_code' => $address['zipCode'],
                'phone' => $customer->phone,
                'order_id' => $order->id,
                'email' => $customer->login
            ]
        );
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
        list($street, $flatNo) = explode(' ', $data['address']['street'] ?? $data['street'], 2);
        CustomerAddress::query()->firstOrCreate(
            [
                'type' => $type,
                'firstname' => $data['firstName'] ?? $data['naturalPerson']['firstName'] ?? null,
                'lastname' => $data['lastName'] ?? $data['naturalPerson']['lastName'] ?? null,
                'address' => $street,
                'flat_number' => $flatNo,
                'city' => $data['address']['city'] ?? $data['city'],
                'firmname' => $data['companyName'] ?? $data['company']['name'] ?? null,
                'nip' => $data['company']['taxId'] ?? null,
                'postal_code' => $data['address']['postCode'] ?? $data['zipCode'],
                'phone' => $customer->phone,
                'customer_id' => $customer->id,
                'email' => $customer->login
            ]
        );
    }
}
