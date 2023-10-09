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
    public static function getPositioningViewHtml(ProductPositioningDTO $productPositioningDTO): string
    {
        return $productPositioningDTO->getProduct()->packing->number_of_trade_items_in_the_largest_unit != 0 ? view('product-positioning', [
            'productPositioningDTO' => $productPositioningDTO,
        ])->render() : '';
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
        $product = $productStockPosition->stock->product;
        $productPacking = $product->packing;

        $IJHWOG = $product->packing->number_of_trade_items_in_the_largest_unit;
        $IJHWOZ = $product->packing->number_of_sale_units_in_the_pack;
        $IOHWOP1 = $product->packing->number_of_trade_items_in_p1;
        $IJHNKWWOZ = $product->stock->number_on_a_layer;
        $IJZNKWWOG = $product->packing->number_of_trade_units_in_full_horizontal_layer_in_global_package;
        $IWJHWPWOZ = $product->packing->number_of_layers_of_trade_units_in_vertical;
        $IPHWOZPD = $product->layers_in_package;
        $IPHWOZPS = $product->packing->number_of_trade_units_in_package_width;
        $IJZPDWOG = $product->packing->number_of_trade_units_in_length_in_global_package;
        $IJZPSWOG = $product->packing->number_of_trade_units_in_width_in_global_package;

        $IOHKSPWZIP1NPWW1WOH = $IOHWOP1 * $IPHWOZPD * $IPHWOZPS;

        $IKWJZWOG = $IJZNKWWOG != 0 && $IJHWOZ != 0 ? floor(($productStockPosition->number_of_trade_items_in_position / $IJHWOZ) / $IJZNKWWOG) : 0;
        $IPJZNRWWOG = $IJHWOZ != 0 && floor(($productStockPosition->number_of_trade_items_in_position - $IKWJZWOG * $IJZNKWWOG * $IJHWOZ) / $IJHWOZ);
        $IJHWROZNRWZWJG = $productStockPosition->number_of_trade_items_in_position - $IKWJZWOG * $IJZNKWWOG * $IJHWOZ - $IPJZNRWWOG * $IJHWOZ;

        $IKROZPDWRWOG = $IJZPSWOG != 0 ? floor($IPJZNRWWOG / $IJZPSWOG) : 0;
        $IKOZWRRNRWWOG = $IPJZNRWWOG - $IKROZPDWRWOG * $IJZPSWOG;

        $IPWJHWROZWOG = floor($IJHWROZNRWZWJG / $IWJHWPWOZ);
        $IKRPDOHWOOZNRWWOG = floor(($IJHWROZNRWZWJG - $IPWJHWROZWOG * $IWJHWPWOZ) / $IPHWOZPS);
        $IOHWRRWROZWRWWOG = $IJHWROZNRWZWJG - $IPWJHWROZWOG * $IWJHWPWOZ - $IKRPDOHWOOZNRWWOG * $IPHWOZPS;

        $IKWW1WROZWRWWROG = floor($IJHWROZNRWZWJG / $IOHKSPWZIP1NPWW1WOH);
        $IKOP1WRWWW1WOG = floor(($IJHWROZNRWZWJG - $IKWW1WROZWRWWROG * $IOHKSPWZIP1NPWW1WOH) / $IOHWOP1);
        $IOHWROP1WRWWOG = $IJHWROZNRWZWJG - $IKWW1WROZWRWWROG * $IOHKSPWZIP1NPWW1WOH - $IKOP1WRWWW1WOG * $IOHWOP1;


        return self::convertArrayToDTO([
            'IJHWOZ' => $IJHWOZ,
            'IJHWOG' => $IJHWOG,
            'IOHWOP1' => $IOHWOP1,
            'IJHNKWWOZ' => $IJHNKWWOZ,
            'IJZNKWWOG' => $IJZNKWWOG,
            'IWJHWPWOZ' => $IWJHWPWOZ,
            'IPHWOZPD' => $IPHWOZPD,
            'IPHWOZPS' => $IPHWOZPS,
            'IJZPDWOG' => $IJZPDWOG,
            'IJZPSWOG' => $IJZPSWOG,
            'IOHKSPWZIP1NPWW1WOH' => $IOHKSPWZIP1NPWW1WOH,
            'IKWJZWOG' => $IKWJZWOG,
            'IPJZNRWWOG' => $IPJZNRWWOG,
            'IJHWROZNRWZWJG' => $IJHWROZNRWZWJG,
            'IKROZPDWRWOG' => $IKROZPDWRWOG,
            'IKOZWRRNRWWOG' => $IKOZWRRNRWWOG,
            'IPWJHWROZWOG' => $IPWJHWROZWOG,
            'IKRPDOHWOOZNRWWOG' => $IKRPDOHWOOZNRWWOG,
            'IOHWRRWROZWRWWOG' => $IOHWRRWROZWRWWOG,
            'IKWW1WROZWRWWROG' => $IKWW1WROZWRWWROG,
            'IKOP1WRWWW1WOG' => $IKOP1WRWWW1WOG,
            'IOHWROP1WRWWOG' => $IOHWROP1WRWWOG,
            'isZero' => true,
            'product' => $product,
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
