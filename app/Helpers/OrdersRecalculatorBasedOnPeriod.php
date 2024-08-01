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
        dd('okej');
        $order->labels()->detach(240);
        $order->labels()->detach(39);
    }
}
