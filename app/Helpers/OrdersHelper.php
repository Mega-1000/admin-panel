<?php

namespace App\Helpers;

use App\Entities\Label;

class OrdersHelper {

    /**
     * @param $order
     * @return mixed
     */
    public static function findSimilarOrders($order): mixed
    {
        $notSentYetLabel = Label::NOT_SENT_YET_LABELS_IDS;
        $batteryId = Label::ORDER_ITEMS_REDEEMED_LABEL;
        $hasHammerOrBagLabel = $order->labels->filter(function ($label) use ($notSentYetLabel) {
            return in_array($label->id, $notSentYetLabel);
        });
        $isNotProducedYet = $order->labels->filter(function ($label) use ($batteryId) {
                return $label->id === $batteryId;
            })->count() == 0;
        if ($hasHammerOrBagLabel && $isNotProducedYet) {
            $history = $order->customer->orders;
            $similar = $history->reduce(function ($acu, $orderh) use ($batteryId, $notSentYetLabel, $order) {
                if ($orderh->id == $order->id) {
                    return $acu;
                }
                $hasChildHammerOrBagLabel = $orderh->labels->filter(function ($label) use ($notSentYetLabel) {
                        return in_array($label->id, $notSentYetLabel);
                    })->count() > 0;
                $isChildNotProducedYet = $orderh->labels->filter(function ($label) use ($batteryId) {
                        return $label->id == $batteryId;
                    })->count() == 0;
                if ($hasChildHammerOrBagLabel && $isChildNotProducedYet) {
                    $acu [] = $orderh->id;
                }
                return $acu;
            }, []);
        }

        return $similar ?? [];
    }
}
