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

    private const LONG_VALUE = 200;

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
        'warehouse',
        'warehouse_physical',
        'ean_of_commercial_packing',
        'ean_of_collective_packing',
        'ean_of_biggest_packing',
        'packing_type',
        'number_of_pieces_in_total_volume',
        'recommended_courier',
        'packing_name',
        'max_pieces_in_one_package',
        'number_of_volume_items_for_paczkomat',
        'dimension_x',
        'dimension_y',
        'dimension_z',
        'allegro_courier',
        'paczkomat_size_a',
        'paczkomat_size_b',
        'paczkomat_size_c'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function isLong()
    {
        return $this->dimension_x > self::LONG_VALUE
            || $this->dimension_y > self::LONG_VALUE
            || $this->dimension_z > self::LONG_VALUE;
    }

    public function getVolume($maxLength = false)
    {
        if ($maxLength) {
            $longDimension = $maxLength;
        } else {
            $longDimension = $this->dimension_x;
        }
        return $longDimension * $this->dimension_y * $this->dimension_z;
    }
}
