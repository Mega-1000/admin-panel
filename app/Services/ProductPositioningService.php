<?php

namespace App\Services;

use App\DTO\ProductPositioning\ProductPositioningDTO;
use App\Entities\Product;
use App\Entities\ProductStockPosition;

class ProductPositioningService
{
    /**
     * Get positioning of product based on its stock position
     *
     * @param ProductStockPosition $productStockPosition
     * @return ProductPositioningDTO
     */
    public static function getPositioning(ProductStockPosition $productStockPosition): ProductPositioningDTO
    {
        $product = $productStockPosition->stock->product;

        if ($product->number_of_trade_items_in_the_largest_unit !== 0) {
            return self::handleNonZeroNumberOfTradeItemsInLargestUnit($productStockPosition, $product);
        }

        return self::handleZeroNumberOfTradeItemsInLargestUnit($productStockPosition, $product);
    }

    /**
     * Handle not zero number of trade items in the largest unit
     *
     * @param ProductStockPosition $productStockPosition
     * @param Product $product
     * @return ProductPositioningDTO
     */
    private static function handleNonZeroNumberOfTradeItemsInLargestUnit(ProductStockPosition $productStockPosition, Product $product): ProductPositioningDTO
    {
        $IWK = $product->packing->number_on_a_layer !== 0 ? floor($productStockPosition->position_quantity / $product->packing->number_on_a_layer) : 0;
        $IJZNWOK = $product->packing->number_of_sale_units_in_the_pack !== 0 ? floor(
            ($productStockPosition->position_quantity - $IWK * $product->packing->number_on_a_layer)
            / $product->packing->number_of_sale_units_in_the_pack
        ) : 0;

        $IJZNWK = $product->layers_in_package !== 0 ? $product->packing->number_of_trade_items_in_the_largest_unit / ($product->layers_in_package * $product->packing->number_of_sale_units_in_the_pack) : 0;

        $IJHWOZ = 0;

        if ($product->packing->number_on_a_layer !== 0 && $product->packing->number_of_sale_units_in_the_pack !== 0) {
            $IJHWOZ = floor($productStockPosition->position_quantity - $IWK * $product->packing->number_on_a_layer - $IJZNWOK * $product->packing->number_of_sale_units_in_the_pack);
        }

        return self::convertArrayToDTO([
            'IJZNWK' => $IJZNWK,
            'IJHWOZ' => $IJHWOZ,
            'IWK' => $IWK,
            'IJZNWOK' => $IJZNWOK,
            'IJHWROZ' => 0,
        ]);
    }

    /**
     * Handle zero number of trade items in the largest unit
     *
     * @param ProductStockPosition $productStockPosition
     * @param Product $product
     * @return ProductPositioningDTO
     */
    private static function handleZeroNumberOfTradeItemsInLargestUnit(ProductStockPosition $productStockPosition, Product $product): ProductPositioningDTO
    {
        $IWK = $product->packing->number_of_sale_units_in_the_pack !== 0 ? floor($productStockPosition->position_quantity / $product->packing->number_of_sale_units_in_the_pack) : 0;

        $IJZHWO = 0;

        if ($product->packing->number_of_sale_units_in_the_pack !== 0) {
            $IJZHWO = floor($productStockPosition->position_quantity - $IWK * $product->packing->number_of_sale_units_in_the_pack);
        }

        return self::convertArrayToDTO([
            'IWK' => $IWK,
            'IJZNWOK' => 0,
            'IJZHWO' => $IJZHWO,
        ]);
    }

    private static function convertArrayToDTO(array $data): ProductPositioningDTO
    {
        return ProductPositioningDTO::fromAcronymsArray($data);
    }
}
