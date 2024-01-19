<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Http\Requests\RecalculateLabelsInOrdersBasedOnPeriodRequest;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\OrderPaymentLabelsService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RecalculateLabelsInOrdersBasedOnPeriod extends Controller
{
    public function __construct(
        protected readonly OrderPaymentLabelsService $orderPaymentLabelsService
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
            if ($request->get('calculate-only-with-39') && DB::table('order_labels')->where('order_id', $order->id)->where('label_id', 39)->count() == 0) {
                continue;
            }

            $this->orderPaymentLabelsService->calculateLabels($order);

            $additional_service = $order->additional_service_cost ?? 0;
            $additional_cod_cost = $order->additional_cash_on_delivery_cost ?? 0;
            $shipment_price_client = $order->shipment_price_for_client ?? 0;
            $totalProductPrice = 0;

            foreach ($order->items as $item) {
                $price = $item->gross_selling_price_commercial_unit ?: $item->net_selling_price_commercial_unit ?: 0;
                $quantity = $item->quantity ?? 0;
                $totalProductPrice += $price * $quantity;
            }

            $depositPaidData = $this->orderDepositPaidCalculator->calculateDepositPaidOrderData($order);

            $sumOfGrossValues = $totalProductPrice + $additional_service + $additional_cod_cost + $shipment_price_client;

            if (
                round($sumOfGrossValues) + round($depositPaidData['returnedValue']) - round($depositPaidData['balance']) - round($depositPaidData['wtonValue']) == 0.0 &&
                $order->payments->count() > 0
            ) {
                $order = Order::find($order->id);
                $LpArray = [];
                RemoveLabelService::removeLabels($order, [39], $LpArray, [], Auth::user()->id);
            } else {
                AddLabelService::addLabels($order, [39], $arr, [], Auth::user()?->id);
            }
        }

        return redirect()->back();
    }
}
