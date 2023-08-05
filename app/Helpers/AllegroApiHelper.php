<?php

namespace App\Helpers;

use Carbon\Carbon;

final class AllegroApiHelper
{
    public static function formatDate(Carbon $date): string
    {
        return $date->format('Y-m-d\TH:i:s\Z');
    }

    public static function getDatesArray(Carbon $startDate, Carbon $endDate): array
    {
        $startDateString = self::formatDate($startDate);
        $endDateString = self::formatDate($endDate);

        return [
            'occurredAt.gte' => $startDateString,
            'occurredAt.lte' => $endDateString,
        ];
    }
}
