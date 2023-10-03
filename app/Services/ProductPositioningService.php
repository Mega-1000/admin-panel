<?php

namespace App\Services;

use App\DTO\ProductPositioning\ProductPositioningDTO;
use App\Entities\Product;
use App\Entities\ProductPacking;
use App\Entities\ProductStockPosition;
use Exception;

class ProductPositioningService
{
    /**
     * @throws Exception
     */
    public static function renderPositioningViewHtml(ProductStockPosition $productStockPosition): string
    {
        return self::getPositioningViewHtml(self::getPositioning($productStockPosition));
    }

    /**
     * Get positioning view html
     *
     * @param ProductPositioningDTO $productPositioningDTO
     * @return string
     */
    private static function getPositioningViewHtml(ProductPositioningDTO $productPositioningDTO): string
    {
        return view('product-positioning', [
            'productPositioningDTO' => $productPositioningDTO,
        ])->render();
    }

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
            $productPacking = $product->packing;

            if ($product->number_of_trade_items_in_the_largest_unit != 0) {
                return self::handleNonZeroNumberOfTradeItemsInLargestUnit($productStockPosition, $product, $productPacking);
            }

            return self::handleZeroNumberOfTradeItemsInLargestUnit($productStockPosition, $product, $productPacking);
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . " (Line: " . $e->getLine() . ")");
        }
    }

    /**
     * Handle not zero number of trade items in the largest unit
     *
     * @param ProductStockPosition $productStockPosition
     * @param Product $product
     * @param ProductPacking $productPacking
     * @return ProductPositioningDTO
     * @throws Exception
     */
    private static function handleNonZeroNumberOfTradeItemsInLargestUnit(
        ProductStockPosition $productStockPosition,
        Product $product,
        ProductPacking $productPacking
    ): ProductPositioningDTO
    {
        $IKWTWJHWOZ = $productPacking->number_on_a_layer != 0 ? floor($productStockPosition->position_quantity / $productPacking->number_on_a_layer) : 0;

        $IJHNOWWROZ = $productStockPosition->position_quantity - $IKWTWJHWOZ * $productPacking->number_on_a_layer;

        $IPROHPDWOWWOZ = $productPacking->number_of_trade_units_in_package_width != 0 ? floor($IJHNOWWROZ / $productPacking->number_of_trade_units_in_package_width) : 0;

        $IOHWRRNOWWOZ = $IJHNOWWROZ - $IPROHPDWOWWOZ * $productPacking->number_of_trade_units_in_package_width;

        return self::convertArrayToDTO([
            'IKWTWJHWOZ' => $IKWTWJHWOZ,
            'IJHNOWWROZ' => $IJHNOWWROZ,
            'IPROHPDWOWWOZ' => $IPROHPDWOWWOZ,
            'IOHWRRNOWWOZ' => $IOHWRRNOWWOZ,
        ]);
    }

    /**
     * Handle zero number of trade items in the largest unit
     *
     * @param ProductStockPosition $productStockPosition
     * @param Product $product
     * @param ProductPacking $productPacking
     * @return ProductPositioningDTO
     * @throws Exception
     */
    private static function handleZeroNumberOfTradeItemsInLargestUnit(
        ProductStockPosition $productStockPosition,
        Product $product,
        ProductPacking $productPacking
    ): ProductPositioningDTO
    {
        $IKWJZWOG = $productPacking->number_of_trade_units_in_full_horizontal_layer_in_global_package != 0 ? floor($productStockPosition->position_quantity / $productPacking->number_of_trade_units_in_full_horizontal_layer_in_global_package) : 0;

        $IPJZNRWWOG = $productPacking->number_on_a_layer != 0 ? floor($productStockPosition->position_quantity - $IKWJZWOG * $productPacking->number_of_trade_units_in_full_horizontal_layer_in_global_package) / $productPacking->number_on_a_layer : 0;

        $IJHWROZNRWZWJG = $productStockPosition->position_quantity - $IKWJZWOG * $productPacking->number_of_trade_units_in_full_horizontal_layer_in_global_package * $productPacking->number_on_a_layer - $IPJZNRWWOG * $productPacking->number_on_a_layer;

        return self::convertArrayToDTO([
            'IKWJZWOG' => $IKWJZWOG,
            'IPJZNRWWOG' => $IPJZNRWWOG,
            'IJHWROZNRWZWJG' => $IJHWROZNRWZWJG,
        ]);
    }

    /**
     * @throws Exception
     */
    private static function convertArrayToDTO(array $data): ProductPositioningDTO
    {
        return ProductPositioningDTO::fromAcronymsArray($data);
    }
}
