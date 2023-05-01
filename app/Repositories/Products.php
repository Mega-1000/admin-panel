<?php

namespace App\Repositories;

use App\Entities\Product;
use App\Enums\ProductVariationGroups;
use Illuminate\Database\Eloquent\Collection;

class Products
{
    /**
     * @return Collection|array
     */
    public static function getStyrofoarmsWithoutVariations(): Collection|array
    {
        // if product_group is same means that product  variation so all have to have unique product_group

        return Product::query()->where('variation_group', ProductVariationGroups::styrofoam)->distinct('product_group')->get();
    }
}
