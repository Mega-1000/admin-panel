<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ProductPacking.
 *
 * @package namespace App\Entities;
 */
class ProductPacking extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'calculation_unit',
        'unit_consumption',
        'unit_commercial',
        'unit_basic',
        'unit_of_collective',
        'unit_biggest',
        'numbers_of_basic_commercial_units_in_pack',
        'number_of_sale_units_in_the_pack',
        'number_of_trade_items_in_the_largest_unit',
        'number_of_items_per_30_kg',
        'ean_of_commercial_packing',
        'ean_of_collective_packing',
        'ean_of_biggest_packing',
        'packing_type',
        'number_of_pieces_in_total_volume',
        'recommended_courier',
        'courier_volume_factor',
        'max_pieces_in_one_package',
        'number_of_items_per_25_kg',
        'number_of_volume_items_for_paczkomat',
        'number_of_items_for_paczkomat',
        'inpost_courier_type',
        'volume_ratio_paczkomat'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
