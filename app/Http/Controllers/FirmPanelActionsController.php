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
                $q->where('firm_id', $firm->id);
            });
        })->get();

        return view('firms.panel', compact('firm', 'orders'));
    }
}
