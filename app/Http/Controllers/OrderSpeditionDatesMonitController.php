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

    public function shippingTodayStore(Order $order): View
    {
        return view('monits.shipping-today-form', compact('order'));
    }
}
