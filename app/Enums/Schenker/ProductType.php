<?php

namespace App\Enums\Schenker;

/**
 * Product Type for createOrder() in SOAP API
 *
 * [ENG] Type of service defined by DB SCHENKER. Available dictionary values
 * [PL] Typ usługi określa produkt transportowy zdefiniowany przez firmę DB SCHENKER. Jedna z wartości słownikowych:
 */
class ProductType
{

    const SYSTEM = 'SYSTEM';
    const PREMIUM_SYSTEM = 'PREMIUM_SYSTEM';

    public static function checkProductTypeExists(string $type): bool
    {
        return in_array(
            $type,
            [
                self::SYSTEM,
                self::PREMIUM_SYSTEM,
            ]
        );
    }

    public static function getDefaultType(): string
    {
        return self::SYSTEM;
    }


}
