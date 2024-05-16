<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderSpeditionDatesMonitController extends Controller
{
    public function notShippingToday(Order $order): string
    {
        $order->last_confirmation = now();
        $order->save();

        return 'Dziękujemy za potwierdzenie, że zamówienie nie zostanie wysłane dziś';
    }

    public function shippingToday(Order $order): View
    {
        return view('monits.shipping-today-form', compact('order'));
    }

    public function shippingTodayStore(Order $order, Request $request): View
    {
        $currentDate = date('Y-m-d');

        $timeFrom = $request->input('time_from');
        $timeTo = $request->input('time_to');

        $order->driver_phone = $request->get('driver_phone');
        $order->special_data_filled = true;
        $order->save();

        $order->dates()->update([
            'consultant_delivery_date_to' => $currentDate . ' ' . $timeTo,
            'warehouse_shipment_date_from' => $currentDate . ' ' . $timeFrom,
            'warehouse_shipment_date_to' => $currentDate . ' ' . $timeTo,
            'warehouse_delivery_date_from' => $currentDate . ' ' . $timeFrom,
            'warehouse_delivery_date_to' => $currentDate . ' ' . $timeTo,
        ]);

        return view('monits.shipping-today-form', compact('order'));
    }
}
