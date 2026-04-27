<?php

namespace App\Services;

use App\Entities\Product;
use Illuminate\Support\Facades\Log;

class ProductPriceCalculator
{
    /**
     * Evaluate pattern_to_set_the_price using the product's parameter values.
     */
    public function evaluatePattern(Product $product): float
    {
        switch ($product->pattern_to_set_the_price) {
            case '[125]':
                return (float) $product->value_of_price_change_data_first;
            case '[126]':
                return (float) $product->value_of_price_change_data_second;
            case '[127]':
                return (float) $product->value_of_price_change_data_third;
            case '[128]':
                return (float) $product->value_of_price_change_data_fourth;
            case '[125]+[126]':
                return (float) $product->value_of_price_change_data_first
                     + (float) $product->value_of_price_change_data_second;
            default:
                Log::error('ProductPriceCalculator: unknown pattern', [
                    'product_id' => $product->id,
                    'pattern'    => $product->pattern_to_set_the_price,
                ]);
                return (float) $product->value_of_price_change_data_first;
        }
    }

    /**
     * Extract unit type (UC/UB/UCA/UCO) from product_group_for_change_price.
     * Format: "[number]-[unitType]", e.g. "10-UC".
     */
    public function extractUnitType(Product $product): string
    {
        $group = $product->product_group_for_change_price;

        if (empty($group) && !empty($product->parentProduct)) {
            $group = $product->parentProduct->product_group_for_change_price;
        }

        if (empty($group)) {
            return 'UC';
        }

        $parts = explode('-', $group);
        return $parts[1] ?? 'UC';
    }

    /**
     * Calculate all net purchase unit prices based on unit type and pattern result.
     */
    public function calculateUnitPrices(float $pattern, string $unitType, $packing): array
    {
        $pack        = (float) ($packing->numbers_of_basic_commercial_units_in_pack ?? 1) ?: 1;
        $unitConsump = (float) ($packing->unit_consumption ?? 1) ?: 1;
        $salePack    = (float) ($packing->number_of_sale_units_in_the_pack ?? 1) ?: 1;
        $largestPack = (float) ($packing->number_of_trade_items_in_the_largest_unit ?? 1) ?: 1;

        switch ($unitType) {
            case 'UB':
                $basic      = $pattern;
                $commercial = $basic * $pack;
                $calc       = $basic * $unitConsump;
                $aggregate  = $commercial * $salePack;
                $largest    = $commercial * $largestPack;
                break;

            case 'UCA':
                $calc       = $pattern;
                $basic      = $calc / $unitConsump;
                $commercial = ($pack / $unitConsump) * $calc;
                $aggregate  = $commercial * $salePack;
                $largest    = $commercial * $largestPack;
                break;

            case 'UCO':
                $aggregate  = $pattern;
                $commercial = $aggregate / $salePack;
                $basic      = $commercial / $pack;
                $calc       = $basic * $unitConsump;
                $largest    = $commercial * $largestPack;
                break;

            case 'UC':
            default:
                $commercial = $pattern;
                $basic      = $commercial / $pack;
                $calc       = $basic * $unitConsump;
                $aggregate  = $commercial * $salePack;
                $largest    = $commercial * $largestPack;
                break;
        }

        return [
            'net_purchase_price_basic_unit'        => $basic,
            'net_purchase_price_commercial_unit'   => $commercial,
            'net_purchase_price_calculated_unit'   => $calc,
            'net_purchase_price_aggregate_unit'    => $aggregate,
            'net_purchase_price_the_largest_unit'  => $largest,
        ];
    }

    /**
     * Calculate net commercial unit price after solid discount + cascade % discounts.
     */
    public function calculateAfterDiscounts(array $unitPrices, Product $product, string $unitType): float
    {
        $price    = $product->price;
        $packing  = $product->packing;

        $solidDiscount = (float) ($price->solid_discount ?? 0);
        if ($unitType === 'UB') {
            $solidDiscount *= (float) ($packing->numbers_of_basic_commercial_units_in_pack ?? 1);
        }

        $commercial = (float) $unitPrices['net_purchase_price_commercial_unit'];

        return ($commercial - $solidDiscount)
            * (100 - (float) ($price->discount1 ?? 0)) / 100
            * (100 - (float) ($price->discount2 ?? 0)) / 100
            * (100 - (float) ($price->discount3 ?? 0)) / 100;
    }

    /**
     * Build the complete price array ready for ProductPrice::update().
     */
    public function buildFullPriceArray(Product $product): array
    {
        $packing     = $product->packing;
        $productPrice = $product->price;

        $unitType = $this->extractUnitType($product);
        $pattern  = $this->evaluatePattern($product);

        // Step 1: tabelaryczne ceny zakupu netto
        $unitPrices = $this->calculateUnitPrices($pattern, $unitType, $packing);

        $pack        = (float) ($packing->numbers_of_basic_commercial_units_in_pack ?? 1) ?: 1;
        $unitConsump = (float) ($packing->unit_consumption ?? 1) ?: 1;

        // Step 2: cena kartotekowa netto po rabatach
        $afterDiscount = $this->calculateAfterDiscounts($unitPrices, $product, $unitType);

        $afterDiscountBasic  = $afterDiscount / $pack;
        $afterDiscountCalc   = $afterDiscount * ($unitConsump / $pack);
        $afterDiscountAgg    = $afterDiscount;
        $afterDiscountLargest= $afterDiscount;

        // Step 3: brutto tabelaryczne (×1.23)
        $vat = 1.23;

        // Step 4: ceny sprzedaży netto + brutto (narzut coating)
        $coating    = (float) ($productPrice->coating ?? 0);
        $coatingMult = (100 + $coating) / 100;

        $netSellCommercial = $afterDiscount    * $coatingMult;
        $netSellBasic      = $afterDiscountBasic * $coatingMult;
        $netSellCalc       = $afterDiscountCalc  * $coatingMult;
        $netSellAgg        = $afterDiscountAgg   * $coatingMult;
        $netSellLargest    = $afterDiscountLargest* $coatingMult;

        $fmt4 = fn (float $v) => (float) number_format($v, 4, '.', '');
        $fmt2 = fn (float $v) => (float) number_format($v, 2, '.', '');

        return [
            // Ceny tabelaryczne netto
            'net_purchase_price_basic_unit'                          => $fmt4($unitPrices['net_purchase_price_basic_unit']),
            'net_purchase_price_commercial_unit'                     => $fmt2($unitPrices['net_purchase_price_commercial_unit']),
            'net_purchase_price_calculated_unit'                     => $fmt4($unitPrices['net_purchase_price_calculated_unit']),
            'net_purchase_price_aggregate_unit'                      => $fmt4($unitPrices['net_purchase_price_aggregate_unit']),
            'net_purchase_price_the_largest_unit'                    => $fmt4($unitPrices['net_purchase_price_the_largest_unit']),

            // Ceny tabelaryczne brutto (×1.23)
            'net_special_price_commercial_unit'                      => $fmt2($unitPrices['net_purchase_price_commercial_unit'] * $vat),
            'net_special_price_basic_unit'                           => $fmt4($unitPrices['net_purchase_price_basic_unit'] * $vat),
            'net_special_price_calculated_unit'                      => $fmt4($unitPrices['net_purchase_price_calculated_unit'] * $vat),
            'net_special_price_aggregate_unit'                       => $fmt4($unitPrices['net_purchase_price_aggregate_unit'] * $vat),
            'net_special_price_the_largest_unit'                     => $fmt4($unitPrices['net_purchase_price_the_largest_unit'] * $vat),

            // Kartotekowe netto po rabatach
            'net_purchase_price_commercial_unit_after_discounts'     => $fmt2($afterDiscount),
            'net_purchase_price_basic_unit_after_discounts'          => $fmt4($afterDiscountBasic),
            'net_purchase_price_calculated_unit_after_discounts'     => $fmt4($afterDiscountCalc),
            'net_purchase_price_aggregate_unit_after_discounts'      => $fmt4($afterDiscountAgg),
            'net_purchase_price_the_largest_unit_after_discounts'    => $fmt4($afterDiscountLargest),

            // Kartotekowe brutto po rabatach
            'gross_purchase_price_commercial_unit_after_discounts'   => $fmt2($afterDiscount * $vat),
            'gross_purchase_price_calculated_unit_after_discounts'   => $fmt2($afterDiscountCalc * $vat),
            'gross_purchase_price_aggregate_unit_after_discounts'    => $fmt2($afterDiscountAgg * $vat),
            'gross_purchase_price_the_largest_unit_after_discounts'  => $fmt2($afterDiscountLargest * $vat),
            'gross_purchase_price_basic_unit_after_discounts'        => $fmt4($afterDiscountBasic * $vat),

            // Ceny sprzedaży netto
            'net_selling_price_commercial_unit'                      => $fmt2($netSellCommercial),
            'net_selling_price_basic_unit'                           => $fmt4($netSellBasic),
            'net_selling_price_calculated_unit'                      => $fmt4($netSellCalc),
            'net_selling_price_aggregate_unit'                       => $fmt4($netSellAgg),
            'net_selling_price_the_largest_unit'                     => $fmt4($netSellLargest),

            // Ceny sprzedaży brutto
            'gross_selling_price_commercial_unit'                    => $fmt2($netSellCommercial * $vat),
            'gross_selling_price_basic_unit'                         => $fmt4($netSellBasic * $vat),
            'gross_selling_price_calculated_unit'                    => $fmt2($netSellCalc * $vat),
            'gross_selling_price_aggregate_unit'                     => $fmt2($netSellAgg * $vat),
            'gross_selling_price_the_largest_unit'                   => $fmt2($netSellLargest * $vat),

            // Cena opakowania i table_price
            'gross_price_of_packing'                                 => $fmt2($netSellCommercial * $vat),
            'table_price'                                            => $fmt2($netSellCommercial * $vat),

            // Dopłata za frezowanie (zachowana z produktu)
            'additional_payment_for_milling'                         => (float) ($productPrice->additional_payment_for_milling ?? 0),
        ];
    }
}
