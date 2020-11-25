<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductStockPacket extends Model
{
    protected $fillable = [
        'packet_quantity', 'packet_name', 'packet_product_quantity', 'product_stock_id'
    ];
}
