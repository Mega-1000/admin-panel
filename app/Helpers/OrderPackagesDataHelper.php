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
        $deliverytemplates = PackageTemplate::all();
        return $sorted = $deliverytemplates->sortBy('list_order');       
    }

    public function calculateShipmentDate( $accept = null, $max = null)
    {
        $now = Carbon::now("Europe/Warsaw");
        if (!is_null($max) && $now->hour > $max) {
            return $this->nearestWorkingDay($now);  
        }
        if (!is_null($accept) &&  $now->hour > $accept ) {
            return $this->nearestWorkingDay($now);
        }
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
