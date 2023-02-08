<?php

namespace App\DTO;

use Carbon\Carbon;

class BaseDTO
{
    protected $optionalFields = [];

    protected function getOptionalFilledFields(): array
    {
        $filledFields = [];
        foreach ($this->optionalFields as $fieldName => $propertyName) {
            if ($this->$propertyName !== null && $this->$propertyName !== '') {
                if ($this->$propertyName instanceof Carbon) {

                    $filledFields[$fieldName] = $this->$propertyName->format(config('integrations.schenker.default_date_time_format', 'Y-m-d\TH:i:s'));
                    continue;
                }
                if (is_array($this->$propertyName)) {
                    if (count($this->$propertyName) > 0) {
                        $filledFields[$fieldName] = $this->$propertyName;
                    }
                    continue;
                }
                $filledFields[$fieldName] = $this->$propertyName;
            }
        }

        return $filledFields;
    }

    protected function convertDate(?Carbon $date, ?string $format = null): ?string
    {
        if ($date !== null) {
            return $date->format(
                $format ?? config('integrations.schenker.default_date_time_format', 'Y-m-d\TH:i:s')
            );
        }
        return null;
    }

    protected function substrText(string $text, $limit = 60, $startFrom = 0): string
    {
        return substr($text, $startFrom, $limit);
    }

    protected function getOnlyNumbers(string $text): string
    {
        return preg_replace('/[^\d]+/', '', $text);
    }

    protected function floatToInt(float $floatValue): int
    {
        return round($floatValue * 100);
    }

}
