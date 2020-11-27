<?php

declare(strict_types=1);

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class OrderPaymentLog extends Model
{
    protected $table = 'order_payments_logs';

    protected $fillable = [
        'booked_date',
        'payment_type',
        'order_payment_id',
        'user_id',
        'payment_service_operator',
        'order_id',
        'description',
        'payment_amount',
        'transfer_payment_amount',
        'client_return_payment_amount',
        'payment_sum_before_payment',
        'payment_sum_after_payment',
        'employee_id'
    ];
}
