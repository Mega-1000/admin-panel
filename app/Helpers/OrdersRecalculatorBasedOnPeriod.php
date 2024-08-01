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

        $payments = OrderPayment::where('order_id', $order->id)->where('declared_sum', '!=', null)->where('status', null)->whereIn('status', [null, 'Deklaracja wpłaty'])->where('promise_date', '<', now())->get()->sum('declared_sum');

        if ($payments != 0) {
            AddLabelService::addLabels($order, [240], $arr, [], Auth::user()?->id);
        } else {
            $order->labels()->detach(240);
        }

        if (OrderPayment::where('order_id', $order->id)->where('declared_sum', '!=', null)->whereIn('status', [null, 'Deklaracja wpłaty'])->where('promise_date', '>', now())->get()->sum('declared_sum') == 0)
        {
            $order->labels()->detach(39);
        } else {
            if (!$order->labels->contains('id', 240)) {
                AddLabelService::addLabels($order, [39], $arr, [], Auth::user()?->id);
            }
        }
        $orderItemsValueWithTransport = $order->getItemsGrossValueForUs() + $order->shipment_price_for_us;
        $totalPaymentsBuying = $order->payments->where('operation_type', 'Wpłata/wypłata bankowa - związana z fakturą zakupową')->sum('amount');

        if ($order->payments->where('operation_type', 'Wpłata/wypłata bankowa - związana z fakturą zakupową')->first()) {
            $arr = [];
            if (
                round($orderItemsValueWithTransport, 2) != round($totalPaymentsBuying, 2) &&
                !$order->labels->contains(257)
            ) {
                AddLabelService::addLabels($order, [258], $arr, []);
            } else {
                RemoveLabelService::removeLabels($order, [258],  $arr, [], Auth::user()?->id);
            }
        }
    }
}
