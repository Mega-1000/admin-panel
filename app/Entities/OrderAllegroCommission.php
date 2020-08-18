<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class OrderAllegroCommission extends Model
{

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
