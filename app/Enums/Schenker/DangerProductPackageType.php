<?php

namespace App\Enums\Schenker;

class DangerProductPackageType
{

    const TYPE_DRUM = 1;
    const TYPE_CANISTER = 2;
    const TYPE_CASE = 3;
    const TYPE_BAG = 4;
    const TYPE_BOTTLE = 5;
    const TYPE_BARREL = 6;
    const TYPE_SOFT_METAL_PACKAGE = 7;
    const TYPE_LARGE_SIZE_CONTAINER = 8;

    public static function checkIfTypeExists(int $typeNumber): bool
    {
        return array_key_exists($typeNumber, self::getDictionary());
    }

    public static function getDictionary(): array
    {
        return [
            self::TYPE_DRUM => 'Bęben',
            self::TYPE_CANISTER => 'Kanister',
            self::TYPE_CASE => 'Skrzynia',
            self::TYPE_BAG => 'Worek',
            self::TYPE_BOTTLE => 'Butelka',
            self::TYPE_BARREL => 'Beczka',
            self::TYPE_SOFT_METAL_PACKAGE => 'Opakowanie metalowe miękkie',
            self::TYPE_LARGE_SIZE_CONTAINER => 'DPPL (Pojemnik wielkogabarytowy)',
        ];
    }

}
