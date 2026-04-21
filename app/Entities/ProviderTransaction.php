<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ProviderTransaction extends Model
{
    protected $table = 'provider_transactions';

    protected $fillable = [
        'provider',
        'waybill_number',
        'invoice_number',
        'order_id',
        'cash_on_delivery',
        'provider_balance',
        'provider_balance_on_invoice',
        'transaction_id'
    ];
}
