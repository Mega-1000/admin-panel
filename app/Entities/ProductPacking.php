<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ProductPacking.
 *
 * @package namespace App\Entities;
 * @property numeric $number_of_trade_items_in_the_largest_unit
 * @property numeric $number_of_sale_units_in_the_pack
 * @property numeric $number_on_a_layer
 * @property string $number_of_layers_of_trade_units_in_vertical_column
 * @property numeric $number_of_trade_units_in_package_width
 * @property numeric $number_of_trade_units_in_full_horizontal_layer_in_global_package
 * @property numeric $number_of_layers_of_trade_units_in_height_in_global_package
 * @property numeric $number_of_trade_units_in_length_in_global_package
 * @property numeric $number_of_trade_units_in_width_in_global_package
 * @property numeric $number_of_trade_items_in_p1
 * @property numeric $number_of_trade_items_in_complete_layer_in_largest_unit
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
        'paczkomat_size_c',
        'number_of_layers_of_trade_units_in_vertical',
        'number_of_layers_of_trade_units_in_vertical_column',
        'number_of_trade_units_in_package_width',
        'number_of_trade_units_in_full_horizontal_layer_in_global_package',
        'number_of_layers_of_trade_units_in_height_in_global_package',
        'number_of_trade_units_in_length_in_global_package',
        'number_of_trade_units_in_width_in_global_package',
        'number_of_trade_items_in_p1',
        'number_of_trade_items_in_complete_layer_in_largest_unit',
    ];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isLong(): bool
    {
        return $this->dimension_x > self::LONG_VALUE
            || $this->dimension_y > self::LONG_VALUE
            || $this->dimension_z > self::LONG_VALUE;
    }

    public function getVolume($maxLength = false): float|int
    {
        if ($maxLength) {
            $longDimension = $maxLength;
        } else {
            $longDimension = $this->dimension_x;
        }
        return $longDimension * $this->dimension_y * $this->dimension_z;
    }
}
