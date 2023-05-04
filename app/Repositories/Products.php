<?php

namespace App\Repositories;

use App\Entities\Product;
use Illuminate\Database\Eloquent\Collection;

class Products
{

    /**
     * Get all products with stock
     *
     * @return Collection
     */
    public static function getAllProductsWithStock(): Collection
    {
        return Product::has('stock.position')->with('packing')->get();
    }
}
