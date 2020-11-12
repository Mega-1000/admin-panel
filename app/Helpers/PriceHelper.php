<?php

namespace App\Helpers;

class PriceHelper
{
    public static function modifyPriceToValidFormat($price)
    {
        return str_replace(",", ".", $price);
    }
}
