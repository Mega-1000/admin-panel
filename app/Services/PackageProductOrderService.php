<?php

namespace App\Services;

use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\Product;
use App\Entities\ProductPrice;
use App\Factory\OrderBuilderFactory;
use Closure;
use Exception;

final class PackageProductOrderService
{
    private array $data = [];

    /**
     * @param Order $order
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function store(Order $order, array $data): void
    {
        $this->data = $data;

        foreach ($data['quantity'] as $key => $quantity) {
            if (empty($quantity)) continue;

            [$productArray, $orderItems] = $this->prepareProductArrayAndOrderItems($order, $key, $quantity);

            $productArray = $this->applyAdditionalOptions(
                order: $order,
                productArray: $productArray,
                quantity: $quantity,
                orderItems: $orderItems,
            );

            $this->reassignOrderItems(
                order: $order,
                quantity: $quantity,
                newProduct: $productArray,
                orderItems: $orderItems,
            );
        }

        $order->save();
    }

    /**
     * @param Order $order
     * @param int|string $key
     * @param float $quantity
     * @return array
     */
    private function prepareProductArrayAndOrderItems(Order $order, int|string $key, float $quantity): array
    {
        $productArray = Product::findOrFail($key)->toArray();

        $productArray['gross_selling_price_commercial_unit'] = ProductPrice::query()
            ->where('product_id', $key)
            ->firstOrFail()
            ->gross_selling_price_commercial_unit;
        $orderItems = $order->items()
            ->with('product')
            ->get()
            ->toArray();

        foreach ($orderItems as &$orderItem) {
            $orderItem['amount'] = 0;
            $orderItem['amount'] = $orderItem['quantity'];
            $orderItem['id'] = $orderItem['product']['id'];
        }

        return [$productArray, $orderItems];
    }

    /**
     * @param Order $order
     * @param array $productArray
     * @param float $quantity
     * @param array $orderItems
     * @return array
     */
    private function applyAdditionalOptions(Order $order, array &$productArray, float $quantity, array &$orderItems): array
    {
        $this->applySingleOption(
            option: 'subtract-from-shipping-cost',
            callback: function () use ($order, &$productArray, $quantity) {
                $order->shipment_price_for_client -= $productArray['gross_selling_price_commercial_unit'] * $quantity;
            }
        );

        $this->applySingleOption(
            option: 'do-not-count-price',
            callback: function () use (&$productArray) {
                $productArray['gross_selling_price_commercial_unit'] = 0;
            }
        );

        $this->applySingleOption(
            option: '0.001-prices',
            callback: fn () => $this->handle001PricesReassignment(
                order: $order,
                productArray: $productArray,
                quantity: $quantity,
                orderItems: $orderItems,
            ),
        );

        return $productArray;
    }

    /**
     * Handle 0.001 price checkbox option - sets price to 0.01 and subtracts it from shipment or order item price
     *
     * @param Order $order
     * @param array $productArray
     * @param float $quantity
     * @param array $orderItems
     * @return void
     */
    public function handle001PricesReassignment(Order $order, array &$productArray, float $quantity, array &$orderItems): void
    {
        $productArray['gross_selling_price_commercial_unit'] = 0.01;

        if ($order->shipment_price_for_client > 0) {
            $order->shipment_price_for_client -= $productArray['gross_selling_price_commercial_unit'] * $quantity;

            return;
        }

        foreach ($orderItems as $orderItem) {
            if ($orderItem['quantity'] === 1) {
                $orderItem['gross_selling_price_commercial_unit'] -= count($this->data) * 0.01;
                return;
            }
        }

        $biggestQuantity = 0;
        $biggestQuantityIndex = 0;
        foreach ($orderItems as $key => $orderItem) {
            if ($orderItem['quantity'] > $biggestQuantity) {
                $biggestQuantity = $orderItem['quantity'];
                $biggestQuantityIndex = $key;
            }
        }

        $orderItems[$biggestQuantityIndex]['gross_selling_price_commercial_unit'] -= count($this->data) * 0.01;
    }

    /**
     * Apply single option from checkbox value
     *
     * @param string $option
     * @param Closure $callback
     * @return void
     */
    private function applySingleOption(string $option, Closure $callback): void
    {
        if (array_key_exists($option, $this->data) && $this->data[$option] === 'on') {
            $callback();
        }
    }

    /**
     * @param Order $order
     * @param float $quantity
     * @param array $newProduct
     * @param array $orderItems
     * @throws Exception
     */
    private function reassignOrderItems(Order $order, float $quantity, array $newProduct, array $orderItems): void
    {
        $order = OrderBuilderFactory::create()
            ->assignItemsToOrder(
                $order,
                [
                    $newProduct + ['amount' => $quantity, 'recalculate' => true],
                    ...$orderItems
                ],
            );

        $order->items->each(
            function (OrderItem $item) use ($newProduct) {
                $item->gross_selling_price_commercial_unit = $newProduct['gross_selling_price_commercial_unit'];
                $item->save();
            }
        );
    }
}
