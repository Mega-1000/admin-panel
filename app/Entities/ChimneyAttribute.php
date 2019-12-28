<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ChimneyAttribute extends Model
{
    public $fillable = ['name'];

    public function category()
    {
        return $this->belongsTo('App\Entities\CategoryDetail');
    }

    public function options()
    {
        return $this->hasMany('App\Entities\ChimneyAttributeOption');
    }
}
