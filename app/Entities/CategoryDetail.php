<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class CategoryDetail extends Model
{
    public function chimneyAttributes()
    {
        return $this->hasMany('App\Entities\ChimneyAttribute');
    }

    public function product()
    {
        return $this->hasOne('App\Entities\Product', 'token_prod_cat', 'token_prod_cat');
    }
}
