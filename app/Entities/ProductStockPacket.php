<?php

declare(strict_types=1);

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductStockPacket extends Model
{
    protected $fillable = [
        'packet_quantity',
        'packet_name',
        'packet_product_quantity',
        'product_stock_id',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ProductStockPacketItem::class, 'product_stock_packet_id');
    }
}
