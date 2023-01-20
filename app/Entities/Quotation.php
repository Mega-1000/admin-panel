<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [ 'text', 'order_id' ];

    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    // change [] tags to real variables values in text
    
}
