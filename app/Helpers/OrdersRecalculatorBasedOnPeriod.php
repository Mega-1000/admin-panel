<?php

namespace App\Helpers;

use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\OrderPaymentLabelsService;
use Illuminate\Support\Facades\Auth;

class OrdersRecalculatorBasedOnPeriod
{
    public static function recalculateOrdersBasedOnPeriod($order): void
    {
        $order->labels()->detach(240);
        $order->labels()->detach(39);

        $arr = [];
        $futurePayments = OrderPayment::where('order_id', $order->id)
            ->where('declared_sum', '!=', null)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', 'Deklaracja wpłaty');
            })
            ->where('promise_date', '>', now())
            ->sum('declared_sum');

        $pastPayments = OrderPayment::where('order_id', $order->id)->where('declared_sum', '!=', null)->where(function ($query) {$query->whereNull('status')->orWhere('status', 'Deklaracja wpłaty');})->where('promise_date', '<', now())->sum('declared_sum');

        dd($pastPayments);

        if ($futurePayments > 0) {
            AddLabelService::addLabels($order, [240], $arr, [], Auth::user()?->id);
            $order->labels()->detach(39);
        } else {
            $order->labels()->detach(240);
        }

        if ($pastPayments > 0 && !$order->labels->contains('id', 240)) {
            AddLabelService::addLabels($order, [39], $arr, [], Auth::user()?->id);
        } else {
            $order->labels()->detach(39);
        }
    }
}
