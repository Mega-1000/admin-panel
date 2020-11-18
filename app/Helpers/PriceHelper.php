<?php

namespace App\Helpers;

class PriceHelper
{
    public static function modifyPriceToValidFormat(string $price) : string
    {
        return str_replace(",", ".", $price);
    }
}
