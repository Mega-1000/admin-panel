<?php

declare(strict_types=1);

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStockPacketItem extends Model
{
    protected $fillable = [
        'product_id',
        'product_stock_packet_id',
        'quantity',
    ];

    public function productStockPacket(): BelongsTo
    {
        return $this->belongsTo(ProductStockPacket::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
