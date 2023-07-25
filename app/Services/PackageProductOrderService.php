<?php

namespace App\Services;

use App\Entities\Order;
use App\Entities\Product;
use App\Entities\ProductPrice;
use App\Factory\OrderBuilderFactory;

class PackageProductOrderService
{
    public function store(Order $order, array $data): void
    {
        foreach ($data['quantity'] as $key => $quantity) {
            if (empty($quantity)) {
                continue;
            }

            $productArray = Product::findOrFail($key)->toArray();

            $productArray['gross_selling_price_commercial_unit'] = ProductPrice::query()
                ->where('product_id', $key)
                ->firstOrFail()
                ->gross_selling_price_commercial_unit;


            if (array_key_exists('subtract-from-shipping-cost', $data) && $data['subtract-from-shipping-cost'] === 'on') {
                $order->shipment_price_for_client -= $productArray['gross_selling_price_commercial_unit'] * $quantity;
            }

            if (array_key_exists('do-not-count-price', $data) && $data['do-not-count-price']) {
                $productArray['gross_selling_price_commercial_unit'] = 0;
            }

            OrderBuilderFactory::create()
                ->assignItemsToOrder(
                    $order,
                    [
                        $productArray +
                        ['amount' => $quantity]
                    ],
                    false,
                );
        }

        $order->save();
    }
}
