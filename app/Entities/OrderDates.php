<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderDates
 * @package App\Entities
 */
class OrderDates extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_shipment_date_from',
        'customer_shipment_date_to',
        'customer_delivery_date_from',
        'customer_delivery_date_to',
        'consultant_shipment_date_from',
        'consultant_shipment_date_to',
        'consultant_delivery_date_from',
        'consultant_delivery_date_to',
        'warehouse_shipment_date_from',
        'warehouse_shipment_date_to',
        'warehouse_delivery_date_from',
        'warehouse_delivery_date_to',
        'customer_acceptance',
        'consultant_acceptance',
        'warehouse_acceptance',
    ];

    /**
     * @return BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
