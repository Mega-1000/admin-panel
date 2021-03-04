<?php

declare(strict_types=1);

namespace App\Helpers;

class ProductSymbolCoreExtractor
{
    const PRODUCT_SYMBOL_CORE_REGEX = '/^(.*?)-/';

    public static function getProductSymbolCore(string $productSymbol)
    {
        return strstr($productSymbol, '-', true) ?: $productSymbol;
    }
}
