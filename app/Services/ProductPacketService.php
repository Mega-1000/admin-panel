<?php

namespace App\Services;

use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\Product;
use App\Entities\ProductPacket;
use App\Factory\OrderBuilderFactory;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductPacketService
{
    /**
     * @throws Exception
     */
    public static function executeForOrder(Order $order): void
    {
        // Create an empty array for products to add
        $toAddArray = [];

        $oldOrderValue = $order->items->sum(function (OrderItem $item) {
            return $item->quantity * $item->gross_selling_price_commercial_unit;
        });

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
                    'min_quantity_in_order' => $explodedArray[4],
                    'quantity_of_packet_in_order' => $explodedArray[5],
                ];

                $productToAddArray = $productToAdd->toArray();

                if (str_contains($dataArray['price'], 'CBS')) {
                } else {
                    if (is_numeric($dataArray['price'])) {
                        $productToAddArray['gross_selling_price_commercial_unit'] = $dataArray['price'];
                    } else {
                        $productToAddArray['gross_selling_price_commercial_unit'] = 0;
                        $product = Product::where('symbol', explode('(', $dataArray['price'])[0])->first();

                        $productToAddArray['gross_selling_price_commercial_unit'] = $product?->price?->allegro_gross_selling_price_after_all_additional_costs ?? 0;
                    }
                }

                if (str_contains($dataArray['price'], '(-)')) {
                    $parts = explode('(-)', $dataArray['price']);
                    $priceAdjustment = isset($parts[1]) ? (float)$parts[1] : 0;
                    $productToAddArray['gross_selling_price_commercial_unit'] -= $priceAdjustment;
                }

                if (str_contains($dataArray['price'], '(+)')) {
                    $parts = explode('(+)', $dataArray['price']);
                    $priceAdjustment = isset($parts[1]) ? (float)$parts[1] : 0;
                    $productToAddArray['gross_selling_price_commercial_unit'] += $priceAdjustment;
                }

                $productToAddArray['amount'] = min(
                    $dataArray['quantity'] * $dataArray['quantity_of_packet_in_order'],
                    $dataArray['max_quantity_in_order']
                );
                Log::notice('twoja stara 331' . $productToAddArray['gross_selling_price_commercial_unit']);

                if ($productToAddArray['amount'] <= $dataArray['min_quantity_in_order']) {
                    return;
                }

                $orderBuilder->assignItemsToOrder($order, [$productToAddArray], false);

                $itemsValue = $order->items->sum(function (OrderItem $item) {
                    return $item->quantity * $item->gross_selling_price_commercial_unit;
                });

                $order->update([
                    'additional_service_cost' => $oldOrderValue - $itemsValue,
                ]);
            }
        }
    }

}
