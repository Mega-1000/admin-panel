<?php

namespace App\Entities;

class Employees
{

    public static function getAllEmployeesByFirmWithActiveStatus(Firm $firm): array
    {
        return $firm->employees()->where('status', 'active')->get()->toArray();
    }
}
