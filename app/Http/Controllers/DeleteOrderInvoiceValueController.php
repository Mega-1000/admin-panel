<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\OrderInvoiceValue;
use App\Repositories\OrderInvoiceValues;
use App\Repositories\Orders;
use App\Services\Label\AddLabelService;
use Illuminate\Http\RedirectResponse;

class DeleteOrderInvoiceValueController extends Controller
{
    public function __invoke(int $id): RedirectResponse
    {
        $order = Order::findOrFail(OrderInvoiceValue::findOrFail($id)->order_id);
        OrderInvoiceValue::findOrFail($id)->delete();

        $orderInvoiceValuesSum = OrderInvoiceValues::getSumOfInvoiceValuesByOrder($order);
        $orderValue = $order->getValue() + Orders::getOrderReturnGoods($order) - Orders::getSumOfWTONPayments($order);
        $arr = [];
        if (round($orderInvoiceValuesSum, 2) != round($orderValue, 2)) {
            AddLabelService::addLabels($order, [231],$arr, []);

            $order->labels()->detach(232);
            return;
        }

        AddLabelService::addLabels($order, [232],$arr, []);
        $order->labels()->detach(231);

        return redirect()->back();
    }
}
