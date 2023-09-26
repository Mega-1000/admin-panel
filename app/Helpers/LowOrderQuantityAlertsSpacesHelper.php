<?php

namespace App\Helpers;

use App\Entities\LowOrderQuantityAlert;
use App\Entities\Order;

class LowOrderQuantityAlertsSpacesHelper
{
    /**
     * Check if space is correct based on order and alert to determine if alert should be sent
     *
     * @param LowOrderQuantityAlert $alert
     * @param Order $order
     * @return bool
     */
    public static function checkIfSpaceIsCorrect(LowOrderQuantityAlert $alert, Order $order): bool
    {
        return match ($alert->space) {
            LowOrderQuantityAlertSpacesEnum::all => true,
            LowOrderQuantityAlertSpacesEnum::allegro => self::isOrderAllegro($order),
            LowOrderQuantityAlertSpacesEnum::eph => self::isOrderEph($order),
            default => false,
        };
    }

    public static function isOrderAllegro(Order $order): bool
    {
        return explode('@', $order->customer->login)[1] === 'allegromail.pl';
    }

    public static function isOrderEph(Order $order): bool
    {
        return !self::isOrderAllegro();
    }
}
