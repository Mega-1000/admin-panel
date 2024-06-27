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
        if ($position !== false) {
            // If a space is found, return the substring starting from the character after the space
            $trimmedName = substr($product->name, $position + 1);
        } else {
            // If no space is found, return the original name
            $trimmedName = $product->name;
        }

        // Check if the last characters are " -" and remove them if present
        if (substr($trimmedName, -2) === ' -') {
            $trimmedName = substr($trimmedName, 0, -2);
        }

        return $trimmedName;
    }
}
