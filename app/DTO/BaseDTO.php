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

                    $filledFields[$fieldName] = $this->$propertyName->format(config('shippings.providers.schenker.default_date_time_format', 'Y-m-dTH:i:s'));
                    continue;
                }
                $filledFields[$fieldName] = $this->$propertyName;
            }
        }

        return $filledFields;
    }

}
