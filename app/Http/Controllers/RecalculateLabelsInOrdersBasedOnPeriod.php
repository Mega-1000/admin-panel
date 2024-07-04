<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Helpers\OrderDepositPaidCalculator;
use App\Helpers\OrdersRecalculatorBasedOnPeriod;
use App\Http\Requests\RecalculateLabelsInOrdersBasedOnPeriodRequest;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\OrderPaymentLabelsService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecalculateLabelsInOrdersBasedOnPeriod extends Controller
{
    public function __construct(
        protected readonly OrderPaymentLabelsService $orderPaymentLabelsService,
        protected readonly OrderDepositPaidCalculator $orderDepositPaidCalculator,
    ) {}

    public function __invoke(RecalculateLabelsInOrdersBasedOnPeriodRequest $request): RedirectResponse
    {
        $query = Order::query()
            ->whereBetween('created_at',  [
                    Carbon::parse($request->get('time-from')),
                    Carbon::parse($request->get('time-to'))
                ]
            );


        $orders = $query->get();

        foreach ($orders as $order) {
            OrdersRecalculatorBasedOnPeriod::recalculateOrdersBasedOnPeriod($order);
        }

        return redirect()->back();
    }
}
