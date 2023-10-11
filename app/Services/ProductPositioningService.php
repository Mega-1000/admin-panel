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
        if ($productStockPosition->id === $productStockPosition->stock->position()->first()->id ) {
            return self::getPositioningViewHtml(self::getPositioning($productStockPosition));
        }

        return '';
    }

    /**
     * Get positioning view html
     *
     * @param ProductPositioningDTO $productPositioningDTO
     * @return string
     */
    public static function getPositioningViewHtml(ProductPositioningDTO $productPositioningDTO): string
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
        $product = $productStockPosition->stock->product;
        $productPacking = $product->packing;

        $IJHWOG = (float)$product->packing->number_of_trade_items_in_the_largest_unit ?? 0;
        $IJHWOZ = (float)$product->packing->number_of_sale_units_in_the_pack ?? 0;
        $IOHWOP1 = (float)$product->packing->number_of_trade_items_in_p1 ?? 0;
        $IJHNKWWOZ = (float)$product->stock->number_on_a_layer ?? 0;
        $IJZNKWWOG = (float)$product->packing->number_of_trade_units_in_full_horizontal_layer_in_global_package ?? 0;
        $IWJHWPWOZ = (float)$product->packing->number_of_layers_of_trade_units_in_vertical ?? 0;
        $IPHWOZPD = (float)$product->layers_in_package ?? 0;
        $IPHWOZPS = (float)$product->packing->number_of_trade_units_in_package_width ?? 0;
        $IJZPDWOG = (float)$product->packing->number_of_trade_units_in_length_in_global_package ?? 0;
        $IJZPSWOG = (float)$product->packing->number_of_trade_units_in_width_in_global_package ?? 0;

        $IOHKSPWZIP1NPWW1WOH = $IOHWOP1 * $IPHWOZPD * $IPHWOZPS;

        $IKWJZWOG = $IJZNKWWOG != 0 && $IJHWOZ != 0 ? floor(($productStockPosition->position_quantity / $IJHWOZ) / $IJZNKWWOG) : 0;
        $IPJZNRWWOG = $IJHWOZ != 0 ? floor(($productStockPosition->position_quantity - $IKWJZWOG * $IJZNKWWOG * $IJHWOZ) / $IJHWOZ) : 0;
        $IJHWROZNRWZWJG = $productStockPosition->position_quantity - $IKWJZWOG * $IJZNKWWOG * $IJHWOZ - $IPJZNRWWOG * $IJHWOZ;

        $IKROZPDWRWOG = $IJZPSWOG != 0 ? floor($IPJZNRWWOG / $IJZPSWOG) : 0;
        $IKOZWRRNRWWOG = $IPJZNRWWOG - $IKROZPDWRWOG * $IJZPSWOG;

        $IPWJHWROZWOG = $IJHNKWWOZ != 0 ? floor($IJHWROZNRWZWJG / $IJHNKWWOZ) : 0;

        $IKRPDOHNRWWRIZBRWWOG = $IPHWOZPS != 0 ? floor(($IJHWROZNRWZWJG - $IPWJHWROZWOG * $IJHNKWWOZ) / $IPHWOZPS) : 0;
        22 - 1 *

        $IOHWRRWROZWRWWOG = $IJHWROZNRWZWJG - $IPWJHWROZWOG * $IJHNKWWOZ - $IKRPDOHNRWWRIZBRWWOG * $IPHWOZPS;

        $IKWW1WROZWRWWROG = $IOHKSPWZIP1NPWW1WOH != 0 ? floor($IJHWROZNRWZWJG / $IOHKSPWZIP1NPWW1WOH) : 0;
        $IKOP1WRWWW1WOG = $IOHWOP1 != 0 ? floor(($IJHWROZNRWZWJG - $IKWW1WROZWRWWROG * $IOHKSPWZIP1NPWW1WOH) / $IOHWOP1) : 0;
        $IOHWROP1WRWWOG = $IJHWROZNRWZWJG - $IKWW1WROZWRWWROG * $IOHKSPWZIP1NPWW1WOH - $IKOP1WRWWW1WOG * $IOHWOP1;

        $IKRPDOHNRWWRIZBRWWOG = $IPHWOZPS != 0 ? floor(($IJHWROZNRWZWJG - $IPWJHWROZWOG * $IJHNKWWOZ) / $IPHWOZPS) : 0;


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
            'IKRPDOHNRWWRIZBRWWOG' => $IKRPDOHNRWWRIZBRWWOG,
            'IOHWRRWROZWRWWOG' => $IOHWRRWROZWRWWOG,
            'IKWW1WROZWRWWROG' => $IKWW1WROZWRWWROG,
            'IKOP1WRWWW1WOG' => $IKOP1WRWWW1WOG,
            'IOHWROP1WRWWOG' => $IOHWROP1WRWWOG,
            'IKRPDOHNRWWRIZBRWWOG' => $IKRPDOHNRWWRIZBRWWOG,
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
