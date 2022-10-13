<?php
/**
 * Author: Sebastian Rogala
 * Mail: sebrogala@gmail.com
 * Created: 17.01.2019
 */

namespace App\Helpers;

use App\Entities\OrderPackage;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Carbon;
use App\Entities\PackageTemplate;
use Illuminate\Support\Facades\Log;

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
                $orderPackage->delivery_date = $customerShipmentDateFrom->addWeekday()->format("Y-m-d");
            }
        }
        try {

            if ($orderPackage->shipment_date instanceof \DateTime) {
                Log::notice('Find Free shipment date for' . $orderPackage->id . ' at ' . $orderPackage->shipment_date->format("Y-m-d"));
            } else {
                Log::notice('Find Free shipment date for' . $orderPackage->id . ' at ' . $orderPackage->shipment_date);

            }
        } catch (\Throwable $ex) {
            Log::error($ex->getMessage());
        }

        return $orderPackage;
    }

    public function generateSticker($order, $data)
    {
        if (!file_exists(storage_path('app/public/' . strtolower($data['delivery_courier_name']) . '/stickers/'))) {
            mkdir(storage_path('app/public/' . strtolower($data['delivery_courier_name'])));
            mkdir(storage_path('app/public/' . strtolower($data['delivery_courier_name']) . '/stickers/'));
        }

        do {
            $data['letter_number'] = $data['order_id'] . rand(1000000, 9999999);
            $path = storage_path('app/public/' . strtolower($data['delivery_courier_name']) . '/stickers/sticker' . $data['letter_number'] . '.pdf');
        } while (file_exists($path));

        $data['sending_number'] = $data['order_id'] . rand(1000000, 9999999);
        $data['shipment_date'] = $data['shipment_date']->format('Y-m-d');
        $data['delivery_date'] = $data['delivery_date']->format('Y-m-d');
        $pdf = PDF::loadView('pdf.sticker', [
            'order' => $order,
            'package' => $data
        ])->setPaper('a5');

        $pdf->save($path);

        return $data;
    }
}
