<?php

namespace App\Helpers\interfaces;

use App\Entities\OrderPackage;

interface iCourier
{
    public function checkStatus(OrderPackage $package): void;
}
