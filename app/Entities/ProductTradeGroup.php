<?php

namespace App\Entities;

use App\Entities\Product;
use Illuminate\Database\Eloquent\Model;

class ProductTradeGroup extends Model
{

    protected $fillable = [
        'product_id',
        'type',
        'first_condition',
        'first_price',
        'second_condition',
        'second_price',
        'third_condition',
        'third_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
