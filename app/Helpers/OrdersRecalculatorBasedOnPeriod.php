<?php

namespace App\Helpers;

use App\Entities\Order;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\OrderPaymentLabelsService;
use Illuminate\Support\Facades\Auth;

class OrdersRecalculatorBasedOnPeriod
{
    public static function recalculateOrdersBasedOnPeriod($order): void
    {
        if (count($order->payments)) {
            if ($order->isPaymentRegulated()) {
                dispatch(new DispatchLabelEventByNameJob($order, "payment-equal-to-order-value"));
            } else {
                if (!$order->labels->contains('id', 240)) {
                    dispatch(new DispatchLabelEventByNameJob($order, "required-payment-before-unloading"));
                }
            }
        }

        $hasMissingDeliveryAddressLabel = $order->labels()->where('label_id', 75)->get();

        if (count($hasMissingDeliveryAddressLabel) > 0) {
            if ($order->isDeliveryDataComplete()) {
                dispatch(new DispatchLabelEventByNameJob($order, "added-delivery-address"));
            }
        }

        app(orderPaymentLabelsService::class)->calculateLabels($order);
        $arr = [];

        $additional_service = $order->additional_service_cost ?? 0;
        $additional_cod_cost = $order->additional_cash_on_delivery_cost ?? 0;
        $shipment_price_client = $order->shipment_price_for_client ?? 0;
        $totalProductPrice = 0;

        foreach ($order->items as $item) {
            $price = $item->gross_selling_price_commercial_unit ?: $item->net_selling_price_commercial_unit ?: 0;
            $quantity = $item->quantity ?? 0;
            $totalProductPrice += $price * $quantity;
        }

        $depositPaidData = app(OrderDepositPaidCalculator::class)->calculateDepositPaidOrderData($order);

        $sumOfGrossValues = $totalProductPrice + $additional_service + $additional_cod_cost + $shipment_price_client;


        dd(round(round($sumOfGrossValues, 2) + round($depositPaidData['returnedValue'], 2) - round($depositPaidData['balance'], 2) - round($depositPaidData['wtonValue'], 2) - round($depositPaidData['externalFirmValue'], 2)));

        $payments = $order->payments()->where('declared_sum', '!=', null)
            ->where('status', '!=', 'Rozliczona deklarowana')
            ->where('promise_date', '>', now())
            ->get()
            ->sum('declared_sum');

        if (
            round(round($sumOfGrossValues, 2) + round($depositPaidData['returnedValue'], 2) - round($depositPaidData['balance'], 2) - round($depositPaidData['wtonValue'], 2) - round($depositPaidData['externalFirmValue'], 2) - round($payments, 2)) == 0.0 &&
            $order->payments->count() > 0
        ) {
            $order = Order::find($order->id);
            $LpArray = [];
            RemoveLabelService::removeLabels($order, [39], $LpArray, [], Auth::user()?->id);
        } else {
            if (!$order->labels->contains('id', 240)) {
                AddLabelService::addLabels($order, [39], $arr, [], Auth::user()?->id);
            }
        }
    }
}
