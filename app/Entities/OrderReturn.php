<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_stock_position_id',
        'user_id',
        'quantity_undamaged',
        'quantity_damaged',
        'description',
        'photo',
    ];

    public function getImageUrl(): ?string
    {
        return $this->photo ? asset('storage/'.str_replace('public/','/',$this->photo)) : null;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
