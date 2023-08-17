<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\ProductStockPosition;
use App\Http\Requests\ConfirmProductStockOrderRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConfirmProductStockOrderController extends Controller
{
    /**
     * Show creation form
     *
     * @param Order $order
     * @return View
     */
    public function create(Order $order): View
    {
        return view('confirm_product_stock_order.create', compact('order'));
    }

    /**
     * Store items to positions
     *
     * @param Order $order
     * @param ConfirmProductStockOrderRequest $request
     * @return RedirectResponse
     */
    public function store(Order $order, ConfirmProductStockOrderRequest $request): RedirectResponse
    {
        foreach ($request->input('position') as $k => $v) {
            foreach ($v as $key => $value) {
                $position = ProductStockPosition::find($key);
                $position->position_quantity += $value;
                $position->save();
            }
        }

        return redirect()->route('orders.index');
    }
}
