<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderPayment.
 *
 * @package namespace App\Entities;
 */
class OrderPayment extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'amount',
        'notices',
        'promise',
        'promise_date',
        'master_payment_id',
        'created_at',
        'type',
        'status',
        'token',
        'transaction_id',
        'external_payment_id',
        'payer',
        'operation_date',
        'tracking_number',
        'operation_id',
        'declared_sum',
        'posting_date',
        'operation_type',
        'comments',
        'rebooked_order_payment_id',
    ];

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getPackageStatus(): string
    {
        return $this->order->packages->first()->status;
    }

    public array $customColumnsVisibilities = [
        'order_id',
        'amount',
        'notices',
        'promise',
        'promise_date',
        'actions',
        'created_at',
        'change_status',
        'external_payment_id',
        'payer',
        'operation_date',
        'tracking_number',
        'operation_id',
        'declared_sum',
        'posting_date',
        'operation_type',
        'comments',
    ];
}
