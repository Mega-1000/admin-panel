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

    /**
     * Get product by id
     *
     * @param int $id
     * @return Product
     */
    public static function getProductByIdWithPrices(int $id): Product
    {
        return Product::join('product_prices', 'products.id', '=', 'product_prices.product_id')
            ->join('product_packings', 'products.id', '=', 'product_packings.product_id')
            ->find($id);
    }
}
