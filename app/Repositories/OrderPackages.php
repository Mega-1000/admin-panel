<?php

namespace App\Repositories;

use App\Entities\OrderPackage;

class OrderPackages
{

    /**
     * @param array|string|null $trackingNumber
     * @return OrderPackage|null
     */
    public function getByLetterNumber(array|string|null $trackingNumber): ?OrderPackage
    {
        if ($trackingNumber !== '' && $trackingNumber !== null) {
            return OrderPackage::where('letter_number', $trackingNumber)->first();
        }
        return null;
    }
}
