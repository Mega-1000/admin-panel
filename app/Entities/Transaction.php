<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    const OLD_COMPANY_NAME_SYMBOL = 'EPHWW';
    const NEW_COMPANY_NAME_SYMBOL = 'EPH';

    protected $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'posted_in_system_date',
        'posted_in_bank_date',
        'payment_id',
        'kind_of_operation',
        'order_id',
        'operator',
        'operation_value',
        'balance',
        'accounting_notes',
        'transaction_notes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payment()
    {
        return $this->hasOne(OrderPayment::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
