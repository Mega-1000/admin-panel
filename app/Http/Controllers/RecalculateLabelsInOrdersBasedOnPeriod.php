<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Http\Requests\RecalculateLabelsInOrdersBasedOnPeriodRequest;
use App\Services\OrderPaymentLabelsService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;

class RecalculateLabelsInOrdersBasedOnPeriod extends Controller
{
    public function __construct(
        protected readonly OrderPaymentLabelsService $orderPaymentLabelsService
    ) {}

    public function __invoke(RecalculateLabelsInOrdersBasedOnPeriodRequest $request): RedirectResponse
    {
        $orders = Order::query()->whereBetween('created_at', [Carbon::create($request->get('date-from')), Cabon::create($request->get('date-to'))])->get();
dd($orders);
        foreach ($orders as $order) {
            $this->orderPaymentLabelsService->calculateLabels($order);
        }

        return redirect()->back();
    }
}
