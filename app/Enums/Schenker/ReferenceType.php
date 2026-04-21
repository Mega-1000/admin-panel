<?php

namespace App\Enums\Schenker;

class ReferenceType
{
    const TYPE_FORWARDING_ORDER = 1;
    const TYPE_WZ_DOCUMENT = 2;
    const TYPE_INVOICE = 3;
    const TYPE_RECIPIENT_ORDER = 4;
    const TYPE_MPK = 5;
    const TYPE_ORDER_BY_CONSIGNOR = 6;
    const TYPE_SHIPPER_NUMBER_IN_CONSIGNEE_SYSTEM = 7;
    const TYPE_SHIPMENT_COVERED_BY_SENT = 21;

    public static function checkIsSupportedType(int $referenceTypeNumber): bool
    {
        return array_key_exists($referenceTypeNumber, self::getDictionary());
    }

    public static function getDictionary(): array
    {
        return [
            self::TYPE_FORWARDING_ORDER => 'Zlecenie spedycyjne',
            self::TYPE_WZ_DOCUMENT => 'Dokument WZ',
            self::TYPE_INVOICE => 'Faktura',
            self::TYPE_RECIPIENT_ORDER => 'Zamówienie odbiorcy',
            self::TYPE_MPK => 'MPK',
            self::TYPE_ORDER_BY_CONSIGNOR => 'Zamówienie wg nadawcy',
            self::TYPE_SHIPPER_NUMBER_IN_CONSIGNEE_SYSTEM => 'Nr Nadawcy w systemie odbiorcy',
            self::TYPE_SHIPMENT_COVERED_BY_SENT => 'Przesyłka objęta SENT',
        ];
    }

}
