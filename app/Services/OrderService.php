<?php

namespace App\Services;

use App\Entities\Customer;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\ProductStock;
use App\Helpers\BackPackPackageDivider;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPriceCalculator;
use App\Repositories\ProductStockLogs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Calculate quantity of order for product stock
     *
     * @param ProductStock $productStock
     * @param int $daysBack
     * @param int $daysToFuture
     * @return int
     */
    public function calculateOrderData(ProductStock $productStock, int $daysBack, int $daysToFuture): int
    {
        $traffic = ProductStockLogs::getTotalQuantityForProductStockInLastDays($productStock, $daysToFuture) / $daysBack * $daysToFuture;

        $currentStock = $productStock->quantity;

        $orderQuantity =  $traffic - $currentStock;

        return max($orderQuantity, 0);
    }

    /**
     * Create order for product stock
     *
     * @param ProductStock $productStock
     * @param array $data
     * @param ProductService $productService
     * @return Order
     */
    public function createOrder(ProductStock $productStock, array $data, ProductService $productService): Order
    {
        $customer = Customer::query()->where('login', $data['clientEmail'])->firstOrFail();

        DB::transaction(function () use ($productStock, $customer, $data, $productService, &$order) {
            $order = Order::query()->create([
                'customer_id' => $customer->id,
                'status_id' => 1,
                'last_status_update_date' => Carbon::now(),
                'total_price' => $productStock->price * 1,
                'customer_notices' => 'Zamówienie stworzone przez administratora'
            ]);

            $product = $productStock->product()->first();

            $products = [];
            $products[0] = [
                'amount' => $this->calculateOrderData($productStock, $data['daysBack'], $data['daysToFuture'])
            ] + $product->toArray();

            $orderBuilder = (new OrderBuilder())
                ->setPackageGenerator(new BackPackPackageDivider())
                ->setPriceCalculator(new OrderPriceCalculator())
                ->setProductService($productService);
            $orderBuilder->assignItemsToOrder($order, $products);
        });

        return $order;
    }
}
