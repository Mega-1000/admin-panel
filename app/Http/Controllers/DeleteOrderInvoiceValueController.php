<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\OrderInvoiceValue;
use App\Repositories\OrderInvoiceValues;
use App\Repositories\Orders;
use App\Services\CalculateSubjectInvoiceBilansLabels;
use App\Services\Label\AddLabelService;
use Illuminate\Http\RedirectResponse;

class DeleteOrderInvoiceValueController extends Controller
{
    public function __invoke(int $id): RedirectResponse
    {
        OrderInvoiceValue::findOrFail($id)->delete();

        CalculateSubjectInvoiceBilansLabels::handle(
            Order::findOrFail(OrderInvoiceValue::findOrFail($id)->order_id),
        );

        return redirect()->back();
    }
}
