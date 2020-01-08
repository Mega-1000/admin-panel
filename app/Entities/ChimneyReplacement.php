<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ChimneyReplacement extends Model
{
    public $fillable = ['product', 'quantity'];

    public function product()
    {
        return $this->belongsTo('App\Entities\ChimneyProduct');
    }
}
