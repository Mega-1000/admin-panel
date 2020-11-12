<?php declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Enums;

use BenSampo\Enum\Enum;

final class DelivererRulesActionEnum extends Enum
{
    public const SEARCH_COMPARE = 'searchCompare';
    public const SEARCH_REGEX = 'searchRegex';
    public const SET = 'set';
    public const GET = 'get';
    public const GET_AND_REPLACE = 'getAndReplace';

    public static function getDescription($value): string
    {
        if ($value === self::SEARCH_COMPARE) {
            return 'Wyszukaj poprzez porównanie';
        }

        if ($value === self::SEARCH_REGEX) {
            return 'Wyszukaj poprzez parsowanie';
        }

        if ($value === self::SET) {
            return 'Ustaw wartość';
        }

        if ($value === self::GET) {
            return 'Pobierz';
        }

        if ($value === self::GET_AND_REPLACE) {
            return 'Pobierz i zastąp';
        }

        return parent::getDescription($value);
    }
}
