<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport;

class PriceFormatter
{
    public static function fromString(string $price): float
    {
        return (float) str_replace(',', '.', $price);
    }

    public static function asAbsolute(float $price): float
    {
        return abs($price);
    }
}
