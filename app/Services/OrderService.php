<?php

namespace App\Services;

use App\DTO\orderPayments\OrderPaymentDTO;
use App\DTO\ProductStocks\CalculateMultipleAdminOrderDTO;
use App\DTO\ProductStocks\CreateMultipleOrdersDTO;
use App\DTO\ProductStocks\ProductStocks\CreateAdminOrderDTO;
use App\Entities\Customer;
use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Entities\Product;
use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;
use App\Enums\OrderPaymentsEnum;
use App\Helpers\BackPackPackageDivider;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPriceCalculator;
use App\Http\Controllers\CreateTWSOUrdersDTO;
use App\Repositories\Customers;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderPayments;
use App\Repositories\ProductStockLogs;
use App\Repositories\Warehouses;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $traffic = (ProductStockLogs::getTotalQuantityForProductStockInLastDays($dto->productStock, $dto->daysBack) / $dto->daysBack) * $dto->daysToFuture;

        $currentStock = $this->getAllProductsQuantity($dto->productStock->id);

        $orderQuantity =  $traffic - $currentStock;

        return [
            'calculatedQuantity' => max($orderQuantity, 0),
            'inOneDay' => ProductStockLogs::getTotalQuantityForProductStockInLastDays($dto->productStock, $dto->daysToFuture) / $dto->daysBack,
            'soldInLastDays' => ProductStockLogs::getTotalQuantityForProductStockInLastDays($dto->productStock, $dto->daysBack),
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

    /**
     * Get all products quantity
     *
     * @param int $id
     * @return int
     */
    public function getAllProductsQuantity(int $id): int
    {
        return ProductStockPosition::query()
            ->where('product_stock_id', $id)
            ->sum('position_quantity');
    }

    /**
     * Get product intervals
     *
     * @param ProductStock $productStock
     * @param int $interval
     * @param int $daysBack
     * @return array
     */
    public function getProductIntervals(ProductStock $productStock, int $interval, int $daysBack): array
    {
        $intervals = [];
        $days = 0;
        for ($i = 0; $i < $daysBack / $interval; $i++) {
            $intervals[$i] = [
                'interval' => $days,
                'quantity' => ProductStockLogs::getTotalQuantityForProductStockPeriod($productStock, ($days - $interval) * -1, $days * -1)
            ];
            $days -= $interval;
        }

        return $intervals;
    }

    /**
     * @param Order $order
     * @param OrderPayment $payment
     * @param OrderPaymentDTO $paymentDTO
     *
     * @return void
     */
    public function rebookStore(Order $order, OrderPayment $payment, OrderPaymentDTO $paymentDTO): void {
        DB::transaction(function() use ($payment, $order, $paymentDTO) {
            OrderPayments::createRebookedOrderPayment(
                $order,
                $paymentDTO,
                OrderPaymentsEnum::REBOOKED_TYPE_IN . " " . (string)$payment->order()->first()->id,
                $payment
            );

            OrderPayments::createRebookedOrderPayment(
                $payment->order()->first(),
                $paymentDTO->setAmount($paymentDTO->getAmount() * -1),
                OrderPaymentsEnum::REBOOKED_TYPE_OUT . " " . $order->id,
                $payment
            );
        });
    }

    public function createTWSOUrders(CreateTWSOUrdersDTO $fromRequest, ProductService $productService): string
    {
        $customer = Customers::getFirstCustomerWithLogin($fromRequest->getClientEmail());

        DB::transaction(function () use ($fromRequest, $customer, $productService, &$order) {
            $order = Order::query()->create([
                'customer_id' => $customer->id,
                'status_id' => 1,
                'last_status_update_date' => Carbon::now(),
                'customer_notices' => 'Zamówienie stworzone przez administratora',
                'warehouse_id' => Warehouses::getIdFromSymbol($fromRequest->getWarehouseSymbol()),
            ]);

            $orderBuilder = (new OrderBuilder())
                ->setPackageGenerator(new BackPackPackageDivider())
                ->setPriceCalculator(new OrderPriceCalculator())
                ->setProductService($productService);
            $product = Product::query()->where('symbol', 'TWSU')->first();
            $orderBuilder->assignItemsToOrder($order, [
                [
                    'amount' => 1,
                ] + $product->toArray()
            ]);

            $item = $order->items()->first();
            $item->gross_selling_price_commercial_unit = $fromRequest->getPurchaseValue();
            $item->save();
        });

        return $order->id;
    }
}
