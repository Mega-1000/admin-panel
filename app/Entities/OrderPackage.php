<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderPackage.
 *
 * @package namespace App\Entities;
 */
class OrderPackage extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'number',
        'size_a',
        'size_b',
        'size_c',
        'shipment_date',
        'delivery_date',
        'service_courier_name',
        'delivery_courier_name',
        'weight',
        'quantity',
        'container_type',
        'shape',
        'cash_on_delivery',
        'notices',
        'status',
        'sending_number',
        'letter_number',
        'cost_for_client',
        'cost_for_company',
        'real_cost_for_company',
        'inpost_url',
        'chosen_data_template',
        'content',
        'send_protocol'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public $customColumnsVisibilities = [
        'number',
        'size_a',
        'size_b',
        'size_c',
        'shipment_date',
        'delivery_date',
        'delivery_courier_name',
        'service_courier_name',
        'weight',
        'quantity',
        'container_type',
        'shape',
        'cash_on_delivery',
        'notices',
        'status',
        'new',
        'sending',
        'waiting_for_sending',
        'delivered',
        'cancelled',
        'sending_number',
        'letter_number',
        'cost_for_client',
        'cost_for_company',
        'real_cost_for_company',
        'created_at',
        'actions',
        'waiting_for_cancelled',
        'reject_cancelled',
    ];

    public function packedProducts()
    {
        return $this->belongsToMany('App\Entities\Product')->withPivot('quantity');
    }
}
