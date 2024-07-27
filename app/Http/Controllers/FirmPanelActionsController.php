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
        ->whereHas('status', function ($q) {
            $q->where('id', 5);
        })
        ->get();

        return view('firms.panel', compact('firm', 'orders'));
    }
}
