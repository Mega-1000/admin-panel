<?php

declare(strict_types=1);

namespace App\Helpers;

class ProductSymbolCoreExtractor
{
    const PRODUCT_SYMBOL_CORE_REGEX = '/^(.*?)-/';

    public static function getProductSymbolCore(string $productSymbol): string
    {
        preg_match(self::PRODUCT_SYMBOL_CORE_REGEX, $productSymbol, $matches);

        return isset($matches[1]) ? $matches[1] : '';
    }
}
