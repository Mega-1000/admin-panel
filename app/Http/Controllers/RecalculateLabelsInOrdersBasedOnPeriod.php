<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Http\Requests\RecalculateLabelsInOrdersBasedOnPeriodRequest;
use App\Services\OrderPaymentLabelsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RecalculateLabelsInOrdersBasedOnPeriod extends Controller
{
    public function __construct(
        protected readonly OrderPaymentLabelsService $orderPaymentLabelsService
    ) {}

    public function __invoke(RecalculateLabelsInOrdersBasedOnPeriodRequest $request): RedirectResponse
    {
        $orders = Order::query()->whereBetween('created_at', [$request->get('date-from'), $request->get('date-to')])->get();

        foreach ($orders as $order) {
            $this->orderPaymentLabelsService->calculateLabels($order);
        }

        return redirect()->back();
    }
}
