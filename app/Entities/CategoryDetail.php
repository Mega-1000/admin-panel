<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class CategoryDetail extends Model
{
    public function chimneyAttributes()
    {
        return $this->hasMany('App\Entities\ChimneyAttribute');
    }
    public function chimneyProducts()
    {
        return $this->hasMany('App\Entities\ChimneyProduct');
    }

    public function product()
    {
        return $this->hasOne('App\Entities\Product', 'token_prod_cat', 'token_prod_cat');
    }
}
