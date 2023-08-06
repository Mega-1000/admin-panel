<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\ProductStockPosition;
use App\Http\Requests\ConfirmProductStockOrderRequest;
use App\Services\Label\AddLabelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $sum = 0;
        foreach ($request->input('position') as $k => $v) {
            foreach ($v as $key => $value) {
                $position = ProductStockPosition::find($key);
                $position->position_quantity += $value;
                $position->save();

                $sum += $value;
            }


            if (OrderItem::find()->quantity != $sum) {
                $arr = [];
                AddLabelService::addLabels($order, [206], $arr, []);
            }

            $sum = 0;
        }

        return redirect()->route('orders.index');
    }
}
