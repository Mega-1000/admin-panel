<?php

namespace App\Services;

use App\DTO\ProductStocks\CalculateMultipleAdminOrderDTO;
use App\DTO\ProductStocks\CreateMultipleOrdersDTO;
use App\DTO\ProductStocks\ProductStocks\CreateAdminOrderDTO;
use App\Entities\Customer;
use App\Entities\Order;
use App\Entities\Product;
use App\Helpers\BackPackPackageDivider;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPriceCalculator;
use App\Repositories\Customers;
use App\Repositories\ProductStockLogs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Calculate quantity of order for product stock
     *
     * @param CalculateMultipleAdminOrderDTO $dto
     * @return array
     */
    public function calculateOrderData(CalculateMultipleAdminOrderDTO $dto): array
    {
        $traffic = ProductStockLogs::getTotalQuantityForProductStockInLastDays($dto->productStock, $dto->daysToFuture) / $dto->daysBack * $dto->daysToFuture;

        $currentStock = $dto->productStock->quantity;

        $orderQuantity =  $traffic - $currentStock;

        return [
            'calculatedQuantity' => max($orderQuantity, 0),
            'inOneDay' => ProductStockLogs::getTotalQuantityForProductStockInLastDays($dto->productStock, $dto->daysToFuture) / $dto->daysBack
        ];
    }

    /**
     * Create order for product stock
     *
     * @param CreateAdminOrderDTO $dto
     * @param ProductService $productService
     * @return Order
     */
    public function createOrder(CreateAdminOrderDTO $dto, ProductService $productService): Order
    {
        $customer = Customers::getFirstCustomerWithLogin($dto->clientEmail);

        DB::transaction(function () use ($dto, $customer, $productService, &$order) {
            $order = Order::query()->create([
                'customer_id' => $customer->id,
                'status_id' => 1,
                'last_status_update_date' => Carbon::now(),
                'total_price' => $dto->productStock->price * 1,
                'customer_notices' => 'Zamówienie stworzone przez administratora'
            ]);

            $product = $dto->productStock->product()->first();

            $products = [];
            $products[0] = [
                'amount' => $this->calculateOrderData(CalculateMultipleAdminOrderDTO::fromRequest($dto->productStock, [
                    'daysBack' => $dto->daysBack,
                    'daysToFuture' => $dto->daysToFuture
                ])),
            ] + $product->toArray();

            $orderBuilder = (new OrderBuilder())
                ->setPackageGenerator(new BackPackPackageDivider())
                ->setPriceCalculator(new OrderPriceCalculator())
                ->setProductService($productService);
            $orderBuilder->assignItemsToOrder($order, $products);
        });

        return $order;
    }

    /**
     * Create multiple orders for product stocks
     *
     * @param CreateMultipleOrdersDTO $dto
     * @param ProductService $productService
     * @return Order
     */
    public function createMultipleOrders(CreateMultipleOrdersDTO $dto, ProductService $productService): Order
    {
        $customer = Customers::getFirstCustomerWithLogin($dto->clientEmail);

        DB::transaction(function () use ($dto, $customer, $productService, &$order) {
            $order = Order::query()->create([
                'customer_id' => $customer->id,
                'status_id' => 1,
                'last_status_update_date' => Carbon::now(),
                'customer_notices' => 'Zamówienie stworzone przez administratora'
            ]);

            $products = $dto->products;
            foreach ($products as &$product) {
                $quantity = $product['quantity'];
                $product = [
                    'amount' => $quantity
                ] + Product::query()->findOrFail($product['id'])->toArray();
            }

            $orderBuilder = (new OrderBuilder())
                ->setPackageGenerator(new BackPackPackageDivider())
                ->setPriceCalculator(new OrderPriceCalculator())
                ->setProductService($productService);
            $orderBuilder->assignItemsToOrder($order, $products);
        });

        return $order;
    }
}
