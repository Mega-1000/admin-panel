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
        $orderLabels = $order->labels()->pluck('id')->toArray();

        if (in_array(Label::RETURN_ALLEGRO_PAYMENTS, $orderLabels) && !in_array(Label::ORDER_ITEMS_REDEEMED_LABEL, $orderLabels)) {
            $order->payments()->create([
                'amount' => $order->getValue(),
                'operation_type' => OrderPaymentsEnum::KWON_STATUS,
                'payer' => $order->customer->login,
            ]);
        }
    }
}
