<?php

declare(strict_types=1);

namespace App\Helpers;

class ProductSymbolCoreExtractor
{
    const PRODUCT_SYMBOL_CORE_REGEX = '/^(.*?)-/';

    public static function getProductSymbolCore(string $productSymbol): string
    {
        if (preg_match(self::PRODUCT_SYMBOL_CORE_REGEX, $productSymbol, $matches)) {
            return $matches[1];
        }

        return '';
    }
}
