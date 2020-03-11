<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class OrderOtherPackage extends Model
{
    public $timestamps = false;

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }
}
