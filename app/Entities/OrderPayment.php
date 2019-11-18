<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderPayment.
 *
 * @package namespace App\Entities;
 */
class OrderPayment extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'amount', 'notices', 'promise', 'promise_date', 'master_payment_id', 'created_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public $customColumnsVisibilities = [
        'order_id',
        'amount' ,
        'notices',
        'promise',
        'promise_date',
        'actions',
        'created_at',
        'change_status',
        ];
}
