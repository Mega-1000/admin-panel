<?php

namespace App\Jobs;

use App\Entities\Customer;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\Product;
use App\Helpers\interfaces\iOrderPriceOverrider;
use App\Helpers\interfaces\iOrderTotalPriceCalculator;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPriceCalculator;
use App\Helpers\OrderPriceOverrider;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerRepositoryEloquent;
use App\Repositories\ProductRepository;
use App\Services\AllegroApiService;
use App\Services\AllegroOrderService;
use App\Services\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
     */
    public function handle()
    {
        $allegroOrders = $this->allegroOrderService->getPendingOrders()['checkoutForms'];
        $allegroOrders = [$allegroOrders[54]];
        foreach ($allegroOrders as $allegroOrder) {
            dump($allegroOrder);
            $order = new Order();
            $customer = $this->findOrCreateCustomer($allegroOrder['buyer']);
            $order->customer_id = $customer->id;
            $order->status_id = 1;
//            $order->totalPrice = $allegroOrder['summary']['totalToPay']['amount'];
            $order->save();

            if ($allegroOrder['messageToSeller'] !== null) {
                $this->createChat($order->id, $customer->id, $allegroOrder['messageToSeller']);
                $order->labels()->attach(MessagesHelper::MESSAGE_YELLOW_LABEL_ID);
            }
            $orderItems = $this->mapItems($allegroOrder['lineItems']);
            $this->saveOrderItems($orderItems, $order);
            $order->total_price = $allegroOrder['summary']['totalToPay']['amount'];
            $order->save();

            exit;
        }
        exit;
    }

    private function mapItems(array $items): array
    {
        $products = [];
        try {

            foreach ($items as $item) {
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
            dd($ex, $product, $item);
        }
        return $products;
    }

    private function saveOrderItems(array $products, Order $order): void
    {
        $weight = 0;
        foreach ($products as $product) {
            $price = $product->price;
            if (!$product || !$price) {
                throw new Exception('wrong_product_id');
            }
            $getStockProduct = $this->productService->getStockProduct($product->id);
            $orderItem = new OrderItem();
            $orderItem->quantity = $product->tt_quantity;
            $orderItem->product_id = $getStockProduct ? $getStockProduct->id : $product->id;
            if (!empty($product->weight_trade_unit)) {
                $weight += $product->weight_trade_unit * $orderItem->quantity;
            }

            foreach (OrderBuilder::getPriceColumns() as $column) {
                if ($column === "gross_selling_price_commercial_unit") {
                    $orderItem->$column = $price->gross_price_of_packing;
                } else {
                    $orderItem->$column = $price->$column;
                }
            }

            $order->items()->save($orderItem);
        }
        $order->weight = round($weight, 2);
        $order->save();
    }

    private function findOrCreateCustomer($buyer): Customer
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
}
