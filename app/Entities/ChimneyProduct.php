<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ChimneyProduct extends Model
{
    public $fillable = ['product_code', 'formula', 'column_number', 'optional'];

    public function category()
    {
        return $this->belongsTo('App\Entities\CategoryDetail');
    }

    public function replacements()
    {
        return $this->hasMany('App\Entities\ChimneyReplacement');
    }
}
