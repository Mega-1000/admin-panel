<?php declare(strict_types=1);

namespace App\Helpers;

class PriceHelper
{
    public static function modifyPriceToValidFormat($price)
    {
        return str_replace(",", ".", $price);
    }
}
