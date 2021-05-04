<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AllegroDispute extends Model
{

    protected $fillable = [
        'dispute_id',
        'hash',
        'status',
        'subject',
        'buyer_id',
        'buyer_login',
        'form_id',
        'order_date',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

}
