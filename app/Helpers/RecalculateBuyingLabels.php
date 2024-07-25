<?php

namespace App\Helpers;

use App\Entities\BuyingInvoice;
use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Entities\Payment;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;

class RecalculateBuyingLabels
{

    public static function recalculate(Order $order): void
    {
        OrderPayment::where('created_at' , '>=', now()->firstOfMonth())
            ->whereHas('order', function ($q) {$q->whereHas('items.product', function ($q) {$q->where('variation_group', 'styropiany');});})->where('operation_type', 'Wpłata/wypłata bankowa')->get()->unique('order')->pluck('order.id');

        if (empty(BuyingInvoice::where('order_id', $order->id)->first()) && !$order->labels->contains('id', 65)) {
            return;
        }

        $sumOfPurchase = 0;

        foreach ($order->items as $item) {
            $pricePurchase = $item['net_purchase_price_commercial_unit_after_discounts'] ?? 0;
            $quantity = $item['quantity'] ?? 0;
            $sumOfPurchase += floatval($pricePurchase) * intval($quantity);
        }

        $totalItemsCost = $sumOfPurchase * 1.23;
        $transportCost = $order->shipment_price_for_us;

        $totalItemsCost += $transportCost;

        $totalGross = BuyingInvoice::where('order_id', $order->id)->sum('value');
        $arr = [];

        if (round($totalGross, 2) == round($totalItemsCost, 2)) {
            AddLabelService::addLabels($order, [264], $arr, []);
            RemoveLabelService::removeLabels($order, [263], $arr , [], auth()->id());
        } else {
            AddLabelService::addLabels($order, [263], $arr, []);
            RemoveLabelService::removeLabels($order, [264], $arr , [], auth()->id());
        }
    }
}
