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
        $position = strpos($product->name, ' '); // Find the position of the first space
        $trimmedName = $position !== false ? substr($product->name, $position + 1) : $product->name;

        // Check if the last character is a hyphen and remove it if true
        if (substr($trimmedName, -1) === '-') {
            $trimmedName = substr($trimmedName, 0, -1);
        }

        return $trimmedName;
    }


}
