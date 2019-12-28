<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ChimneyAttributeOption extends Model
{
    public $fillable = ['name'];
    
    public function attribute()
    {
        return $this->belongsTo('App\Entities\ChimneyAttribute');
    }
}
