<?php

namespace App\Http\Controllers;
use App\Entities\Order;

class AllegroReturnPaymentController extends Controller
{
    public function index(int $orderId) {
        $order = Order::with(['items', 'orderReturn'])->find($orderId);

        if (empty($order)) {
            abort(404);
        }

        return view('allegro-return.index', [
            'order' => $order,
        ]);
    }
}
