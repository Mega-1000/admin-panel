<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderItem.
 *
 * @package namespace App\Entities;
 */
class OrderItem extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'quantity',
        'net_purchase_price_commercial_unit',
        'net_purchase_price_basic_unit',
        'net_purchase_price_calculated_unit',
        'net_purchase_price_aggregate_unit',
        'net_purchase_price_the_largest_unit',
        'net_purchase_price_commercial_unit_after_discounts',
        'net_purchase_price_basic_unit_after_discounts',
        'net_purchase_price_calculated_unit_after_discounts',
        'net_purchase_price_aggregate_unit_after_discounts',
        'net_purchase_price_the_largest_unit',
        'net_selling_price_commercial_unit',
        'net_selling_price_basic_unit',
        'net_selling_price_calculated_unit',
        'net_selling_price_aggregate_unit',
        'net_selling_price_the_largest_unit',
        'gross_selling_price_commercial_unit',
        'gross_selling_price_basic_unit',
        'gross_selling_price_calculated_unit',
        'gross_selling_price_aggregate_unit',
        'gross_selling_price_the_largest_unit',
        'product_stock_packet_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function realProduct()
    {
        return $this->product->stock->quantity;
    }

    public function realProductPositions()
    {
        return $this->product->stock->position;
    }

    public function packet(): BelongsTo
    {
        return $this->belongsTo(ProductStockPacket::class, 'product_stock_packet_id');
    }
}
