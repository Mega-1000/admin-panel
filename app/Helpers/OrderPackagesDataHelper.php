<?php
/**
 * Author: Sebastian Rogala
 * Mail: sebrogala@gmail.com
 * Created: 17.01.2019
 */

namespace App\Helpers;

use App\Entities\OrderPackage;
use Illuminate\Support\Carbon;
use App\Entities\PackageTemplate;

class OrderPackagesDataHelper extends DateHelper
{
    public function getData()
    {
        $deliveryTemplates = PackageTemplate::all();
        return $deliveryTemplates->sortBy('list_order');
    }

    public function calculateShipmentDate($accept = null, $max = null): string
    {
        $now = Carbon::now("Europe/Warsaw");
        if (!is_null($max) && $now->hour > $max) {
            return $this->nearestWorkingDay($now);
        }
        if (!is_null($accept) && $now->hour > $accept) {
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

    /**
     * Znajduje pierwszą odpowiednią date dla przesyłki.
     *
     * @param OrderPackage $orderPackage Obiekt przesyłki zamówienia.
     *
     * @return OrderPackage
     *
     * @author Norbert Grzechnik <norbert.grzechnik@netro42.digital>
     */
    public function findFreeShipmentDate(OrderPackage $orderPackage): OrderPackage
    {
        $customerShipmentDateFrom = $orderPackage->order->dates->customer_shipment_date_from;
        $shipmentDate = Carbon::make($orderPackage->shipment_date);

        if (file_exists(storage_path('app/public/protocols/day-close-protocol-' . $orderPackage->delivery_courier_name . '-' . Carbon::today()->toDateString() . '.pdf'))) {
            if ($customerShipmentDateFrom > $shipmentDate) {
                $orderPackage->shipment_date = $customerShipmentDateFrom->format("Y-m-d");
            } else {
                $orderPackage->shipment_date = $shipmentDate->addWeekday()->format("Y-m-d");;
            }
        }

        return $orderPackage;
    }
}
