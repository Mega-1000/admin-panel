<?php

namespace App\Helpers;

use App\Entities\Product;

class AuctionsHelper
{
    /**
     * @param Product $product
     * @return string
     */
    public static function getTrimmedProductGroupName(Product $product): string
    {
        $trimmedString = ltrim($product->product_group, '|');
        preg_match('/^(\w+)\s+(\w+)/', $trimmedString, $matches);

        return $matches ? $matches[0] : '';
    }
}
