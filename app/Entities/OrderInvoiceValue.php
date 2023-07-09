<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderInvoiceValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'value'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
