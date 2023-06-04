<?php

namespace App\Repositories;

use App\Entities\Category;
use App\Entities\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categories
{
    public static function getElementsForCsvReloadJob(): Collection
    {
        return Category::query()
            ->Where('save_name', '!=', true)
            ->orWhere('save_description','!=', true)
            ->orWhere('save_image','!=', true)
            ->orWhere('artificially_created','!=', false)
            ->get();
    }

    public static function getElementsForCsvReloadJobByParentId($id): Collection
    {
        return Category::query()
            ->where('parent_id', $id)
            ->orderBy('parent_id')
            ->orderBy('priority')
            ->get();
    }

    public static function getProductsForSymbols($symbols)
    {
        return Product::whereIn('products.symbol', $symbols)
            ->where('products.show_on_page', '=', 1)
            ->join('product_prices', 'products.id', '=', 'product_prices.product_id')
            ->join('product_packings', 'products.id', '=', 'product_packings.product_id')
            ->orderBy('priority')
            ->orderBy('name')
            ->get();
    }


    public static function getProductsForCategory(Category $category): HasMany
    {
        return $category
            ->products()
            ->select('product_prices.*', 'product_packings.*', 'products.*')
            ->where('products.show_on_page', '=', 1)
            ->join('product_prices', 'products.id', '=', 'product_prices.product_id')
            ->with('media')
            ->join('product_packings', 'products.id', '=', 'product_packings.product_id')
            ->orderBy('priority')
            ->orderBy('name');
    }
}
