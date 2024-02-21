<?php

namespace App\Helpers;

use App\Entities\Product;

class AuctionsHelper
{
    /**
     * @param Product $product
     * @return string
     */
    public static function getTrimmedProductGroupName(mixed $product): string
    {
        return strstr($product->name,' ');
    }
}
