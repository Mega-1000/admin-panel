<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPacket extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_symbol',
        'packet_products_symbols',
        'packet_name',
    ];
}
