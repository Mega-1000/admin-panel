<?php

namespace App\Services;

use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\ProductPacket;
use App\Entities\ProductPacking;
use App\Factory\OrderBuilderFactory;
use Exception;

class ProductPacketService
{
    /**
     * @throws Exception
     */
    public static function executeForOrder(Order $order): void
    {
        $toAddArray = collect();
        $orderProducts = $order->items->filter(function (OrderItem $product) {
            var_dump($product->product->symbol);
            return ProductPacket::where('product_symbol', $product->product->symbol)->exists();
        });
        var_dump($orderProducts);

        $orderProducts->each(function (OrderItem $product) use (&$toAddArray) {
            $productPacking = ProductPacket::query()->where('product_symbol', $product->product->symbol)->first();
            $toAddArray->push(explode(' ', json_decode($productPacking->packet_products_symbols)));
            $product->delete();
        });

        $toAddArray = $toAddArray->flatten()->unique();

        $orderBuilder = OrderBuilderFactory::create();

        foreach ($toAddArray as $productToAddSymbol) {
            $productToAdd = Product::query()->where('symbol', $productToAddSymbol[0])->first()->toArray();

            $productPrice = $productToAdd[1];
            $productToAdd['gross_selling_price_commercial_unit'] = $productPrice;

            $orderBuilder->assignItemsToOrder($order, $productToAdd);
        }
    }
}
