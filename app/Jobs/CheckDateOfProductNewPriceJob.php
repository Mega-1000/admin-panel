<?php

namespace App\Jobs;

use App\Repositories\ProductPriceRepository;
use App\Repositories\ProductRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckDateOfProductNewPriceJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $repository;

    protected $productPriceRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ProductRepository $repository, ProductPriceRepository $productPriceRepository)
    {
        $this->repository = $repository;
        $this->productPriceRepository = $productPriceRepository;

        $products = $this->repository->findWhere([['date_of_the_new_prices', '<=', Carbon::today()->addDay()]]);

        foreach ($products as $product) {
            $group = $product->product_group_for_change_price;
            if ($group !== null) {
                $exp = explode('-', $group);
                $groupExp = $exp[1];
            } else {
                Log::error(
                    'Price group is null',
                    ['product_id' => $product->id, 'class' => get_class($this), 'line' => __LINE__]
                );
                die();
            }
            $pattern = $this->setPatternKey($product);
            switch ($groupExp) {
                case 'UB':
                    $price['net_purchase_price_basic_unit'] = $pattern;
                    $price['net_purchase_price_commercial_unit'] = (float)$price['net_purchase_price_basic_unit'] * $product->packing->numbers_of_basic_commercial_units_in_pack;
                    $price['net_purchase_price_calculated_unit'] = (float)$price['net_purchase_price_basic_unit'] * $product->packing->unit_consumption;
                    $price['net_purchase_price_aggregate_unit'] = ((float)$price['net_purchase_price_basic_unit'] * $product->packing->numbers_of_basic_commercial_units_in_pack) * $product->packing->number_of_sale_units_in_the_pack;
                    $price['net_purchase_price_the_largest_unit'] = ((float)$price['net_purchase_price_basic_unit'] * $product->packing->numbers_of_basic_commercial_units_in_pack) * $product->packing->number_of_trade_items_in_the_largest_unit;
                    break;
                case 'UC':
                    $price['net_purchase_price_commercial_unit'] = $pattern;
                    $price['net_purchase_price_basic_unit'] = (float)$price['net_purchase_price_commercial_unit'] / $product->packing->numbers_of_basic_commercial_units_in_pack;
                    $price['net_purchase_price_calculated_unit'] = ((float)$price['net_purchase_price_commercial_unit'] / $product->packing->numbers_of_basic_commercial_units_in_pack) * $product->packing->unit_consumption;
                    $price['net_purchase_price_aggregate_unit'] = (float)$price['net_purchase_price_commercial_unit'] * $product->packing->number_of_sale_units_in_the_pack;
                    $price['net_purchase_price_the_largest_unit'] = (float)$price['net_purchase_price_commercial_unit'] * $product->packing->number_of_trade_items_in_the_largest_unit;
                    break;
                case 'UCA':
                    $price['net_purchase_price_calculated_unit'] = $pattern;
                    $price['net_purchase_price_basic_unit'] = (float)$price['net_purchase_price_calculated_unit'] / $product->packing->unit_consumption;
                    $price['net_purchase_price_commercial_unit'] = ($product->packing->numbers_of_basic_commercial_units_in_pack / $product->packing->unit_consumption) * (float)$price['net_purchase_price_calculated_unit'];
                    $price['net_purchase_price_aggregate_unit'] = ($product->packing->numbers_of_basic_commercial_units_in_pack / $product->packing->unit_consumption) * (float)$price['net_purchase_price_calculated_unit'] * $product->packing->number_of_sale_units_in_the_pack;
                    $price['net_purchase_price_the_largest_unit'] = ($product->packing->numbers_of_basic_commercial_units_in_pack / $product->packing->unit_consumption) * (float)$price['net_purchase_price_calculated_unit'] * $product->packing->number_of_trade_items_in_the_largest_unit;
                    break;
                case 'UCO':
                    $price['net_purchase_price_aggregate_unit'] = $pattern;
                    $price['net_purchase_price_basic_unit'] = ((float)$price['net_purchase_price_aggregate_unit'] / $product->packing->number_of_sale_units_in_the_pack) / $product->packing->numbers_of_basic_commercial_units_in_pack;
                    $price['net_purchase_price_commercial_unit'] = (float)$price['net_purchase_price_aggregate_unit'] / $product->packing->number_of_sale_units_in_the_pack;
                    $price['net_purchase_price_calculated_unit'] = ((float)$price['net_purchase_price_aggregate_unit'] / $product->packing->number_of_sale_units_in_the_pack) / $product->packing->numbers_of_basic_commercial_units_in_pack * $product->packing->unit_consumption;
                    $price['net_purchase_price_the_largest_unit'] = ((float)$price['net_purchase_price_aggregate_unit'] / $product->packing->number_of_sale_units_in_the_pack) * $product->packing->number_of_trade_items_in_the_largest_unit;
                    break;
                default:
                    Log::error(
                        'Invalid price group',
                        ['priceGroup' => $groupExp, 'class' => get_class($this), 'line' => __LINE__]
                    );
                    die();
            }
            //cena kartotekowa netto zakupu bez bonusa dla jednostek
            $price['net_purchase_price_commercial_unit_after_discounts'] = number_format($this->calculatePriceAfterDiscounts($price, $product) * $product->price->euro_exchange,
                2, '.', '');
            $price['net_purchase_price_basic_unit_after_discounts'] = number_format(($this->calculatePriceAfterDiscounts($price, $product) * $product->price->euro_exchange) / $product->packing->numbers_of_basic_commercial_units_in_pack,
                4, '.', '');
            $price['net_purchase_price_calculated_unit_after_discounts'] = number_format($this->calculatePriceAfterDiscounts($price, $product) * ($product->packing->unit_consumption / $product->packing->numbers_of_basic_commercial_units_in_pack) * $product->price->euro_exchange,
                4, '.', '');
            $price['net_purchase_price_aggregate_unit_after_discounts'] = number_format($this->calculatePriceAfterDiscounts($price, $product) * $product->price->euro_exchange,
                4, '.', '');
            $price['net_purchase_price_the_largest_unit_after_discounts'] = number_format($this->calculatePriceAfterDiscounts($price, $product) * $product->price->euro_exchange,
                4, '.', '');

            //cena tabelaryczna brutto zakupu dla jednostek

            $price['net_special_price_commercial_unit'] = number_format($price['net_purchase_price_commercial_unit'] * 1.23,
                2, '.', '');
            $price['net_special_price_basic_unit'] = number_format($price['net_purchase_price_basic_unit'] * 1.23,
                4, '.', '');
            $price['net_special_price_calculated_unit'] = number_format($price['net_purchase_price_calculated_unit'] * 1.23,
                4, '.', '');
            $price['net_special_price_aggregate_unit'] = number_format($price['net_purchase_price_aggregate_unit'] * 1.23,
                4, '.', '');
            $price['net_special_price_the_largest_unit'] = number_format($price['net_purchase_price_the_largest_unit'] * 1.23,
                4, '.', '');

            //cena kartotekowa brutto zakupu bez bonusa dla jednostek
            $price['gross_purchase_price_basic_unit_after_discounts'] = number_format($price['net_purchase_price_basic_unit_after_discounts'] * 1.23,
                2, '.', '');
            $price['gross_purchase_price_commercial_unit_after_discounts'] = number_format($price['net_purchase_price_commercial_unit_after_discounts'] * 1.23,
                2, '.', '');
            $price['gross_purchase_price_calculated_unit_after_discounts'] = number_format($price['net_purchase_price_calculated_unit_after_discounts'] * 1.23,
                2, '.', '');
            $price['gross_purchase_price_aggregate_unit_after_discounts'] = number_format($price['net_purchase_price_aggregate_unit_after_discounts'] * 1.23,
                2, '.', '');
            $price['gross_purchase_price_the_largest_unit_after_discounts'] = number_format($price['net_purchase_price_the_largest_unit_after_discounts'] * 1.23,
                2, '.', '');

            //cena netto sprzedazy dla jednostek
            $price['net_selling_price_basic_unit'] = number_format($price['net_purchase_price_basic_unit_after_discounts'] * ((100 + $product->price->coating) / 100),
                2, '.', '');
            $price['net_selling_price_commercial_unit'] = number_format($price['net_purchase_price_commercial_unit_after_discounts'] * ((100 + $product->price->coating) / 100),
                2, '.', '');
            $price['net_selling_price_calculated_unit'] = number_format($price['net_purchase_price_calculated_unit_after_discounts'] * ((100 + $product->price->coating) / 100),
                2, '.', '');
            $price['net_selling_price_aggregate_unit'] = number_format($price['net_purchase_price_aggregate_unit_after_discounts'] * ((100 + $product->price->coating) / 100),
                2, '.', '');
            $price['net_selling_price_the_largest_unit'] = number_format($price['net_purchase_price_the_largest_unit_after_discounts'] * ((100 + $product->price->coating) / 100),
                2, '.', '');
            //cena netto sprzedazy dla jednostek
            $price['gross_selling_price_basic_unit'] = number_format($price['net_purchase_price_basic_unit_after_discounts'] * ((100 + $product->price->coating) / 100) * 1.23,
                2, '.', '');
            $price['gross_selling_price_commercial_unit'] = number_format($price['net_purchase_price_commercial_unit_after_discounts'] * ((100 + $product->price->coating) / 100) * 1.23,
                2, '.', '');
            $price['gross_selling_price_calculated_unit'] = number_format($price['net_purchase_price_calculated_unit_after_discounts'] * ((100 + $product->price->coating) / 100) * 1.23,
                2, '.', '');
            $price['gross_selling_price_aggregate_unit'] = number_format($price['net_purchase_price_aggregate_unit_after_discounts'] * ((100 + $product->price->coating) / 100) * 1.23,
                2, '.', '');
            $price['gross_selling_price_the_largest_unit'] = number_format($price['net_purchase_price_the_largest_unit_after_discounts'] * ((100 + $product->price->coating) / 100) * 1.23,
                2, '.', '');

            $productsRelated = \App\Entities\Product::where('products_related_to_the_automatic_price_change', $product->symbol)->get();

            $ids = [$product->id];
            foreach ($productsRelated as $productRelated) {
                $ids[] = $productRelated->id;
            }

            \App\Entities\ProductPrice::whereIn('product_id', $ids)->update($price);
        }
    }

    public function setPatternKey($product)
    {
        switch ($product->pattern_to_set_the_price) {
            case '[125]':
                $data = (float)$product['value_of_price_change_data_first'];
                break;
            case '[126]':
                $data = (float)$product['value_of_price_change_data_second'];
                break;
            case '[127]':
                $data = (float)$product['value_of_price_change_data_third'];
                break;
            case '[128]':
                $data = (float)$product['value_of_price_change_data_fourth'];
                break;
            case '[125]+[126]':
                $data = (float)$product['value_of_price_change_data_first'] + (float)$product['value_of_price_change_data_second'];
                break;
            default:
                Log::error(
                    'Invalid price pattern',
                    ['pattern' => $product->pattern_to_set_the_price, 'class' => get_class($this), 'line' => __LINE__]
                );
                die();
        }
        return $data;
    }

    private function calculatePriceAfterDiscounts($price, $product)
    {
        return (
            (
                (float)$price
                * (100 - $product->price->discount1)
                * (100 - $product->price->discount2)
                * (100 - $product->price->discount3)
                + $product->price->solid_discount
            ) / 1000000);
    }
}
