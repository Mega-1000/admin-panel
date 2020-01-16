<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ChimneyAttribute extends Model
{
    public $fillable = ['name', 'column_number'];

    public function category()
    {
        return $this->belongsTo('App\Entities\Category');
    }

    public function options()
    {
        return $this->hasMany('App\Entities\ChimneyAttributeOption');
    }
}
