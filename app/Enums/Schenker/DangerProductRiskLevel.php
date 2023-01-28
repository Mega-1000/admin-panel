<?php

namespace App\Enums\Schenker;

class DangerProductRiskLevel
{

    const LEVEL_HIGH_RISK = 'I';
    const LEVEL_MEDIUM_RISK = 'II';
    const LEVEL_LOW_RISK = 'III';

    public static function checkRiskLevelExists(string $riskLevel): bool
    {
        return in_array($riskLevel, [
            self::LEVEL_HIGH_RISK,
            self::LEVEL_MEDIUM_RISK,
            self::LEVEL_LOW_RISK,
        ]);
    }

}
