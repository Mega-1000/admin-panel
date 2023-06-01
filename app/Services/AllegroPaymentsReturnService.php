<?php

namespace App\Services;

use App\Entities\Label;
use App\Entities\Order;
use App\Enums\OrderPaymentsEnum;

class AllegroPaymentsReturnService
{
    /**
     * @param Order $order
     * @return void
     */
    public static function checkAllegroReturn(Order $order): void
    {
        /** @var $orderLabels */
        $orderLabels = $order->labels()->pluck('labels.id')->toArray();

        if (
            in_array(Label::RETURN_ALLEGRO_PAYMENTS, $orderLabels) &&
            !in_array(Label::ORDER_ITEMS_REDEEMED_LABEL, $orderLabels) &&
            !self::checkIfOrderHasKwonPayment($order)
        ) {
            $order->payments()->create([
                'amount' => $order->getValue() * -1,
                'operation_type' => OrderPaymentsEnum::KWON_STATUS,
                'payer' => $order->customer->login,
            ]);
        }
    }

    /**
     * @param Order $order
     * @return bool
     */
    private static function checkIfOrderHasKwonPayment(Order $order): bool
    {
        return $order->payments()->where('operation_type', OrderPaymentsEnum::KWON_STATUS)->exists();
    }
}
