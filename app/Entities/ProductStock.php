<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ProductStock.
 * @property int $id
 * @property int $quantity
 * @property Collection<ProductStockPosition> $position
 * @package namespace App\Entities;
 */
class ProductStock extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'quantity',
        'min_quantity',
        'unit',
        'start_quantity',
        'number_on_a_layer'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function position(): HasMany
    {
        return $this->hasMany(ProductStockPosition::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany(ProductStockLog::class);
    }

    public function packets(): HasMany
    {
        return $this->hasMany(ProductStockPacket::class)->where('packet_quantity', '>', 0);
    }

    public $customColumnsVisibilities = [
        'name',
        'symbol',
        'url',
        'status',
        'manufacturer',
        'quantity',
        'min_quantity',
        'unit',
        'start_quantity',
        'number_on_a_layer',
        'created_at',
        'net_purchase_price_commercial_unit',
        'net_purchase_price_commercial_unit_after_discounts',
        'net_special_price_commercial_unit',
        'net_purchase_price_basic_unit',
        'net_purchase_price_basic_unit_after_discounts',
        'net_special_price_basic_unit',
        'net_purchase_price_calculated_unit',
        'net_purchase_price_calculated_unit_after_discounts',
        'net_special_price_calculated_unit',
        'gross_purchase_price_aggregate_unit',
        'gross_purchase_price_aggregate_unit_after_discounts',
        'gross_special_price_aggregate_unit',
        'gross_purchase_price_the_largest_unit',
        'gross_purchase_price_the_largest_unit_after_discounts',
        'gross_special_price_the_largest_unit',
        'net_selling_price_commercial_unit',
        'net_selling_price_basic_unit',
        'net_selling_price_calculated_unit',
        'net_selling_price_aggregate_unit',
        'net_selling_price_the_largest_unit',
        'discount1',
        'discount2',
        'discount3',
        'bonus1',
        'bonus2',
        'bonus3',
        'gross_price_of_packing',
        'table_price',
        'vat',
        'additional_payment_for_milling',
        'coating'
    ];
}
