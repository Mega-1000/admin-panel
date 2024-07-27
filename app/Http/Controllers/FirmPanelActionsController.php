<?php

namespace App\Http\Controllers;

use App\Entities\Firm;
use App\Entities\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FirmPanelActionsController extends Controller
{
    public function __invoke(Firm $firm): View
    {
        $orders = Order::whereHas('items', function ($query) use ($firm) {
            $query->whereHas('product', function ($q) use ($firm) {
                $q->where('manufacturer', $firm->symbol);
            });
        })
        ->orderBy('created_at', 'desc')
        ->whereHas('orderWarehouseNotifications')
        ->get();

        return view('firms.panel', compact('firm', 'orders'));
    }

    public function show(Order $order): View
    {
        return view('firms.show-order', compact('order'));
    }
}
