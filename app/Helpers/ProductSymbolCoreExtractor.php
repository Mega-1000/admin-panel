<?php

declare(strict_types=1);

namespace App\Helpers;

class ProductSymbolCoreExtractor
{
    public static function getProductSymbolCore(string $productSymbol): string
    {
        return strstr($productSymbol, '-', true) ?: $productSymbol;
    }
}
