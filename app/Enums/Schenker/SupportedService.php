<?php

namespace App\Enums\Schenker;

class SupportedService
{

    const TYPE_UNLOADING_OF_CARGO = 2;
    const TYPE_PAYMENT_ON_DELIVERY = 9;
    const TYPE_EMAIL_NOTIFICATIONS = 82;
    const TYPE_PHONE_NOTIFICATIONS = 83;

    public static function isSupportedService(int $serviceNumber): bool
    {
        return array_key_exists($serviceNumber, self::getDictionary());
    }

    public static function getDictionary(): array
    {
        return [
            self::TYPE_UNLOADING_OF_CARGO => 'Rozładunek (automatyczna)',
            self::TYPE_PAYMENT_ON_DELIVERY => 'Płatność za pobraniem (automatyczna)',
            self::TYPE_EMAIL_NOTIFICATIONS => 'Powiadomienie mailowe (automatyczna)',
            self::TYPE_PHONE_NOTIFICATIONS => 'Powiadomienie telefoniczne (automatyczna)',
        ];
    }

    public static function getDefaultServices(): array
    {
        return [
            self::TYPE_UNLOADING_OF_CARGO,
            self::TYPE_PAYMENT_ON_DELIVERY,
            self::TYPE_EMAIL_NOTIFICATIONS,
            self::TYPE_PHONE_NOTIFICATIONS,
        ];
    }

}
