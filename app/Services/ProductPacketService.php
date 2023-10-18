<?php

namespace App\Services;

use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\Product;
use App\Entities\ProductPacket;
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
            return ProductPacket::where('product_symbol', $product->product->symbol)->exists();
        });

        // Loop through order products and populate $toAddArray
        $orderProducts->each(function (OrderItem $product) use (&$toAddArray) {
            $productPacking = ProductPacket::where('product_symbol', $product->product->symbol)->first();
            $packetProductsSymbols = json_decode($productPacking->packet_products_symbols);

            foreach ($packetProductsSymbols as &$symbol) {
                $symbol .=  ' ' . $product->quantity;
            }

            $toAddArray = array_merge($toAddArray, $packetProductsSymbols);
            $product->delete();
        });

        // Use array_unique to remove duplicates from $toAddArray
        $toAddArray = array_unique($toAddArray);
        $toAddArray = collect($toAddArray)->flatten()->toArray();

        // Create an instance of OrderBuilderFactory
        $orderBuilder = OrderBuilderFactory::create();

        foreach ($toAddArray as $productToAddSymbol) {
            // Find the product by symbol in the Product model
            $productToAdd = Product::where('symbol', explode(' ', $productToAddSymbol)[0])->first();

            if ($productToAdd) {
                $explodedArray = explode(' ', $productToAddSymbol);
                $dataArray = [
                    'symbol' => $explodedArray[0],
                    'price' => $explodedArray[1],
                    'quantity' => $explodedArray[2] ,
                    'max_quantity_in_order' => $explodedArray[3],
                    'quantity_of_packet_in_order' => $explodedArray[4],
                ];

                $productToAddArray = $productToAdd->toArray();
                $productToAddArray['gross_selling_price_commercial_unit'] = $dataArray['price'];

                $productToAddArray['amount'] = min(
                    $dataArray['quantity'] * $dataArray['quantity_of_packet_in_order'],
                    $dataArray['max_quantity_in_order']
                );

                // Assign the product to the order using OrderBuilderFactory
                $orderBuilder->assignItemsToOrder($order, [ $productToAddArray ], false);
            }
        }
    }

}
