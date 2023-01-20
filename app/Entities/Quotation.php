<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [ 'text', 'order_id' ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
}
