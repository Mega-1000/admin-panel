<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ProductPrice.
 *
 * @package namespace App\Entities;
 */
class ProductPrice extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'net_purchase_price_commercial_unit',
        'net_purchase_price_commercial_unit_after_discounts',
        'net_special_price_commercial_unit',
        'net_purchase_price_basic_unit',
        'net_purchase_price_basic_unit_after_discounts',
        'net_special_price_basic_unit',
        'net_purchase_price_calculated_unit',
        'net_purchase_price_calculated_unit_after_discounts',
        'net_special_price_calculated_unit',
        'net_purchase_price_aggregate_unit',
        'net_purchase_price_aggregate_unit_after_discounts',
        'net_special_price_aggregate_unit',
        'net_purchase_price_the_largest_unit',
        'net_purchase_price_the_largest_unit_after_discounts',
        'net_special_price_the_largest_unit',
        'net_selling_price_commercial_unit',
        'net_selling_price_basic_unit',
        'net_selling_price_calculated_unit',
        'net_selling_price_aggregate_unit',
        'net_selling_price_the_largest_unit',
        'discount1',
        'discount2',
        'discount3',
        'solid_discount',
        'bonus1',
        'bonus2',
        'bonus3',
        'gross_price_of_packing',
        'table_price',
        'vat',
        'additional_payment_for_milling',
        'coating',
        'euro_exchange',
        'gross_selling_price_basic_unit',
        'gross_purchase_price_basic_unit_after_discounts',
        'gross_selling_price_commercial_unit',
        'gross_purchase_price_commercial_unit_after_discounts',
        'gross_selling_price_calculated_unit',
        'gross_purchase_price_calculated_unit_after_discounts',
        'gross_selling_price_aggregate_unit',
        'gross_purchase_price_aggregate_unit_after_discounts',
        'gross_selling_price_the_largest_unit',
        'gross_purchase_price_the_largest_unit_after_discounts',
        'allegro_selling_gross_commercial_price'
    ];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
