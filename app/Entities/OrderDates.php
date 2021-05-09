<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
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
        'order_id',
        'client_preferred_shipment_date',
        'client_preferred_delivery_date',
        'consultant_preferred_shipment_date',
        'consultant_preferred_delivery_date',
        'warehouse_preferred_shipment_date',
        'warehouse_preferred_delivery_date',
        'customer_acceptance',
        'consultant_acceptance',
        'warehouse_acceptance',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
