<?php

namespace App\Services;

use App\DTO\Messages\CreateMessageDTO;
use App\DTO\orderPayments\OrderPaymentDTO;
use App\DTO\ProductStocks\CalculateMultipleAdminOrderDTO;
use App\DTO\ProductStocks\CreateMultipleOrdersDTO;
use App\DTO\ProductStocks\ProductStocks\CreateAdminOrderDTO;
use App\Entities\Chat;
use App\Entities\ChatUser;
use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Entities\Product;
use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;
use App\Enums\OrderPaymentsEnum;
use App\Enums\UserRole;
use App\Helpers\BackPackPackageDivider;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPriceCalculator;
use App\Http\Controllers\CreateTWSOOrdersDTO;
use App\Repositories\Customers;
use App\Repositories\OrderPayments;
use App\Repositories\Orders;
use App\Repositories\ProductStockLogs;
use App\Repositories\Warehouses;
use App\Services\Label\AddLabelService;
use Carbon\Carbon;
use http\Message;
use Illuminate\Support\Facades\Auth;
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
        $traffic = (ProductStockLogs::getTotalQuantityForProductStockInLastDays($dto->productStock, $dto->daysBack) / $dto->daysBack) * $dto->daysToFuture;

        $currentStock = $this->getAllProductsQuantity($dto->productStock->id);

        $orderQuantity = $traffic - $currentStock;

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

            $order->getDeliveryAddress()->update([
                'firstname' => 'dimitr',
                'lastname' => 'Bolbot',
                'address' => ' ul lotnicza 9',
                'postal_code' => '55-200',
                'flat_number' => '9',
                'city' => 'stanowice',
            ]);

            $order->getInvoiceAddress()->update([
                'firmname' => 'Elektroniczna Platforma handlowa Sp z o o',
                'address' => 'ul.  Jaracza 22/12',
                'postal_code' => '50-305 ',
                'city' => 'Wrocław',
                'nip' => '8982272269',
            ]);

            $order->employee()->associate(12);
            $order->update('status_id', 3);

            $order->labels()->detach();
            $arr = [];

            AddLabelService::addLabels($order, [93], $arr, []);
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
                'customer_notices' => 'Zamówienie stworzone przez administratora',
                'is_buying_admin_side' => true,
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
    public function rebookStore(Order $order, OrderPayment $payment, OrderPaymentDTO $paymentDTO): void
    {
        DB::transaction(function () use ($payment, $order, $paymentDTO) {
            OrderPayments::createRebookedOrderPayment(
                $order,
                $paymentDTO,
                OrderPaymentsEnum::REBOOKED_TYPE_IN . " " . (string)$payment->order()->first()->id,
                $payment,
            );

            OrderPayments::createRebookedOrderPayment(
                $payment->order()->first(),
                $paymentDTO->setAmount($paymentDTO->getAmount() * -1),
                OrderPaymentsEnum::REBOOKED_TYPE_OUT . " " . $order->id,
                $payment,
            );
        });
    }

    /**
     * @throws ChatException
     */
    public function createTWSOOrders(
        CreateTWSOOrdersDTO $fromRequest,
        ProductService      $productService,
        MessageService      $messageService
    ): string
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
                    'gross_selling_price_commercial_unit' => $fromRequest->getPurchaseValue(),
                    'recalculate' => true,
                ] + $product->toArray()
            ]);

            $item = $order->items()->first();
            $item->save();

            $order->employee_id = 12;
            $order->save();
        });

        $messageService->addMessage(
            new CreateMessageDTO(
                message: $fromRequest->getConsultantDescription(),
                area: UserRole::Consultant,
                token: (new MessagesHelper)->getChatToken($order->id, auth()->user()->id),
            ),
        );

        return $order->id;
    }

    /**
     * @param Order $order
     * @return float
     */
    public static function calculateTotalCost(Order $order): float
    {
        $sumOfPurchase = 0;
        $items = $order->items;

        foreach ($items as $item) {
            $pricePurchase = $item->net_purchase_price_commercial_unit_after_discounts ?? 0;
            $quantity = $item->quantity ?? 0;

            $sumOfPurchase += floatval($pricePurchase) * $quantity;
        }

        $totalItemsCost = $sumOfPurchase * 1.23;
        $transportCost = 0;

        if ($order->shipment_price_for_us) {
            $transportCost = floatval($order->shipment_price_for_us);
        }

        return $totalItemsCost + $transportCost;
    }

    public function calculateInvoiceReturnsLabels(Order $order): void
    {
        $sumOfReturns = Orders::getSumOfBuyingInvoicesReturns($order);

        if ((int)$sumOfReturns === 0) {
            return;
        }

        $arr = [];
        if (self::calculateTotalCost($order) !== $sumOfReturns) {
            AddLabelService::addLabels($order, [235], $arr, [], Auth::user()?->id);

            return;
        }

        AddLabelService::addLabels($order, [236], $arr, [], Auth::user()?->id);
    }
}
