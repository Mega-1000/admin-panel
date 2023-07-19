<?php

namespace App\Helpers\interfaces;

use App\Entities\OrderPackage;

interface ICourier
{
    public function checkStatus(OrderPackage $package): void;
}
