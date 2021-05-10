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
        'order_id',
        'client_preferred_shipment_date_from',
        'client_preferred_shipment_date_to',
        'client_preferred_delivery_date_from',
        'client_preferred_delivery_date_to',
        'consultant_preferred_shipment_date_from',
        'consultant_preferred_shipment_date_to',
        'consultant_preferred_delivery_date_from',
        'consultant_preferred_delivery_date_to',
        'warehouse_preferred_shipment_date_from',
        'warehouse_preferred_shipment_date_to',
        'warehouse_preferred_delivery_date_from',
        'warehouse_preferred_delivery_date_to',
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
