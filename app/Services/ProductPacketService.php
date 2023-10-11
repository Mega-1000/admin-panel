<?php

namespace App\Services;

use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\ProductPacking;
use App\Factory\OrderBuilderFactory;
use App\Helpers\OrderBuilder;
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
            return in_array($product->product->symbol, ProductPacking::pluck('product_symbol')->all()->toArray());
        });

        $orderProducts->each(function (OrderItem $product) use (&$toAddArray) {
            $productPacking = ProductPacking::query()->where('product_symbol', $product->product->symbol)->first();
            $toAddArray->push(json_decode($productPacking->packet_products_symbols));
            $product->delete();
        });

        $toAddArray = $toAddArray->flatten()->unique();

        $orderBuilder = OrderBuilderFactory::create();

        foreach ($toAddArray as $productToAddSymbol) {
            $productToAdd = Product::query()->where('symbol', $productToAddSymbol)->first()->toArray();

            $orderBuilder->assignItemsToOrder($order, $productToAdd);
        }
    }
}
