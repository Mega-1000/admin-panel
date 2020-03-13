<?php
/**
 * Author: Sebastian Rogala
 * Mail: sebrogala@gmail.com
 * Created: 17.01.2019
 */

namespace App\Helpers;

use Illuminate\Support\Carbon;
use App\Entities\PackageTemplate;

class OrderPackagesDataHelper extends DateHelper
{
    public function getData()
    {
        return $deliverytemplates = PackageTemplate::all();
    }

    public function calculateShipmentDate()
    {
        $now = Carbon::now("Europe/Warsaw");

        if ($now->hour < 16 && $this->isThatDateWorkingDay($now)) {
            return $now->format("Y-m-d");
        }

        return $this->nearestWorkingDay($now);
    }

    public function calculateDeliveryDate($shipmentDate)
    {
        $date = Carbon::make($shipmentDate);

        return $this->nearestWorkingDay($date);
    }
}
