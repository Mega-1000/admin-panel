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
        // Create an empty array for products to add
        $toAddArray = [];

        // Get the order items that match product symbols in the ProductPacking model
        $orderProducts = $order->items->filter(function (OrderItem $product) {
            return ProductPacking::where('product_symbol', $product->product->symbol)->exists();
        });

        // Loop through order products and populate $toAddArray
        $orderProducts->each(function (OrderItem $product) use (&$toAddArray) {
            $productPacking = ProductPacking::where('product_symbol', $product->product->symbol)->first();
            $packetProductsSymbols = json_decode($productPacking->packet_products_symbols);
            if (!empty($packetProductsSymbols)) {
                $toAddArray = array_merge($toAddArray, $packetProductsSymbols);
            }
            $product->delete();
        });

        // Use array_unique to remove duplicates from $toAddArray
        $toAddArray = array_unique($toAddArray);

        // Create an instance of OrderBuilderFactory
        $orderBuilder = OrderBuilderFactory::create();

        foreach ($toAddArray as $productToAddSymbol) {
            // Find the product by symbol in the Product model
            $productToAdd = Product::where('symbol', $productToAddSymbol)->first();

            if ($productToAdd) {
                // Convert the product to an array and add pricing information
                $productToAddArray = $productToAdd->toArray();
                $productToAddArray['gross_selling_price_commercial_unit'] = $productToAdd->price;

                // Assign the product to the order using OrderBuilderFactory
                $orderBuilder->assignItemsToOrder($order, $productToAddArray);
            }
        }
    }

}
