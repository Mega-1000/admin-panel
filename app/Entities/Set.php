<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    protected $table = 'sets';

    public function products()
    {
        return SetItem::where('set_id', $this->id)
            ->leftJoin('products', 'products.id', '=', 'product_sets.product_id')
            ->select(['product_sets.*', 'products.*', 'product_sets.id as id'])
            ->get()
            ->all();
    }
}
