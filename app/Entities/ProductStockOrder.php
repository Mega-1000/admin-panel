<?php

namespace App\entites;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockOrder extends Model
{
    use HasFactory;

    public function offers()
    {
        return $this->hasMany(ProductStockOrderOffer::class);
    }

    public function stock()
    {
        return $this->belongsTo(ProductStock::class);
    }
}
