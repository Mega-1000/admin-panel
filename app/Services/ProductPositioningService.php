<?php

namespace App\Services;

use App\DTO\ProductPositioning\ProductPositioningDTO;
use App\Entities\Product;
use App\Entities\ProductStockPosition;
use Exception;

class ProductPositioningService
{
    /**
     * Get positioning of product based on its stock position
     *
     * @param ProductStockPosition $productStockPosition
     * @return ProductPositioningDTO
     * @throws Exception
     */
    public static function getPositioning(ProductStockPosition $productStockPosition): ProductPositioningDTO
    {
        try {
            $product = $productStockPosition->stock->product;

            if ($product->number_of_trade_items_in_the_largest_unit !== 0) {
                return self::handleNonZeroNumberOfTradeItemsInLargestUnit($productStockPosition, $product);
            }

            return self::handleZeroNumberOfTradeItemsInLargestUnit($productStockPosition, $product);
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . " (Line: " . $e->getLine() . ")");
        }
    }

    /**
     * Handle not zero number of trade items in the largest unit
     *
     * @param ProductStockPosition $productStockPosition
     * @param Product $product
     * @return ProductPositioningDTO
     * @throws Exception
     */
    private static function handleNonZeroNumberOfTradeItemsInLargestUnit(ProductStockPosition $productStockPosition, Product $product): ProductPositioningDTO
    {
        try {
            $IWK = $product->packing->number_on_a_layer !== 0 ? floor($productStockPosition->position_quantity / $product->packing->number_on_a_layer) : 0;
            $IJZNWOK = $product->packing->number_of_sale_units_in_the_pack !== 0 ? floor(
                ($productStockPosition->position_quantity - $IWK * $product->packing->number_on_a_layer)
                / $product->packing->number_of_sale_units_in_the_pack
            ) : 0;

            // Handle division by zero errors
            $IJZNWK = 0;
            if ($product->layers_in_package !== 0 && $product->packing->number_of_sale_units_in_the_pack !== 0) {
                $IJZNWK = $product->packing->number_of_trade_items_in_the_largest_unit / ($product->layers_in_package * $product->packing->number_of_sale_units_in_the_pack);
            }

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
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . " (Line: " . $e->getLine() . ")");
        }
    }

    /**
     * Handle zero number of trade items in the largest unit
     *
     * @param ProductStockPosition $productStockPosition
     * @param Product $product
     * @return ProductPositioningDTO
     * @throws Exception
     */
    private static function handleZeroNumberOfTradeItemsInLargestUnit(ProductStockPosition $productStockPosition, Product $product): ProductPositioningDTO
    {
        try {
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
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . " (Line: " . $e->getLine() . ")");
        }
    }

    /**
     * @throws Exception
     */
    private static function convertArrayToDTO(array $data): ProductPositioningDTO
    {
        try {
            return ProductPositioningDTO::fromAcronymsArray($data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . " (Line: " . $e->getLine() . ")");
        }
    }
}
