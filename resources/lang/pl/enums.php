<?php

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Enums\AllegroExcel\AllegroHeaders;
use App\Enums\AllegroExcel\OrderHeaders;
use App\Enums\AllegroExcel\PaymentsHeader;
use App\Enums\AllegroExcel\SheetNames;
use App\Enums\CourierStatus\DpdPackageStatus;

return [
    DelivererRulesColumnNameEnum::class => [
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_ADDITIONAL_SERVICE => 'ALLEGRO nazwa usługi dodatkowej',
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_DEPOSIT_VALUE => 'ALLEGRO wartość wpłaty',
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_FORM_ID => 'ALLEGRO numer zamówienia (allegro_form_id)',
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_OPERATION_DATE => 'ALLEGRO data operacji',
        DelivererRulesColumnNameEnum::ORDER_PAYMENT_CHANNEL => 'Kanał wpłaty',
        DelivererRulesColumnNameEnum::ORDER_REFUND_ID => 'ALLEGRO ID zwrotu',
        DelivererRulesColumnNameEnum::ORDER_PACKAGES_LETTER_NUMBER => 'Numer listu przewozowego',
        DelivererRulesColumnNameEnum::ORDER_PACKAGES_REAL_COST_FOR_COMPANY_COST => 'Rzeczywiste koszty brutto transportu przesyłki',
        DelivererRulesColumnNameEnum::ORDER_PACKAGES_SERVICE_COURIER_NAME => 'Symbol firmy obsługującej przesyłkę',
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_COMMISSION => 'ALLEGRO wartość prowizji od sprzedaży towaru',
        DelivererRulesColumnNameEnum::SEL_TR_TRANSACTION_SELLO_PAYMENT => 'ALLEGRO Id płatności',
        DelivererRulesColumnNameEnum::SEL_TR_TRANSACTION_SELLO_FORM => 'ALLEGRO numer zamówienia (sello_form)',
    ],
    DpdPackageStatus::class => [
        DpdPackageStatus::DELIVERED => 'Przesyłka doręczona',
        DpdPackageStatus::SENDING => 'Przesyłka odebrana przez Kuriera',
        DpdPackageStatus::WAITING_FOR_SENDING => 'Zarejestrowano dane przesyłki',
        DpdPackageStatus::INWAREHOUSE => 'Przyjęcie przesyłki w oddziale DPD',
        DpdPackageStatus::INDELIVERY => 'Wydanie przesyłki do doręczenia',

    ],
    OrderHeaders::class => [
        OrderHeaders::ORDER_ID => 'ID zlecenia - (numer zlecenia)',
        OrderHeaders::PACKAGE_LETTER_NUMBER => 'Przesyłka - numer listu przewozowego (numer lp)',
        OrderHeaders::CASH_ON_DELIVERY_AMOUNT => 'Przesyłka - wartość pobrań dla danego numeru listu przewozowego',
        OrderHeaders::ALLEGRO_ORDER_ID => 'ALLEGRO - ID operacji (numer zamówienia)',
        OrderHeaders::ORDER_ITEMS_SUM => 'Oferta - wartość towaru brutto',
        OrderHeaders::ADDITIONAL_SERVICE_COST => 'Oferta DKO - dodatkowy koszt obsługi brutto w ofercie',
        OrderHeaders::ADDITIONAL_CASH_ON_DELIVERY_COST => 'Oferta DKP - dodatkowy koszt pobrania brutto w ofercie',
        OrderHeaders::ORDER_PROFIT => 'Zysk brutto oferty',
        OrderHeaders::ORDER_SUM => 'Wartość oferty - całkowita wartość oferty brutto',
        OrderHeaders::CLIENT_PACKAGE_COST => 'Zakładany koszt brutto przesyłki dla nas',
        OrderHeaders::FIRM_PACKAGE_COST => 'Zakładany koszt brutto przesyłki dla klienta',
        OrderHeaders::REAL_PACKAGE_COST => 'Oferta - Rzeczywisty koszt brutto przesyłki dla nas',
        OrderHeaders::SHIPMENT_PRICE_FOR_CLIENT => 'koszt transportu dla całego zlecenia brutto (po uwzględnieniu ustawienia ręcznego)',
    ],
    AllegroHeaders::class => [
        AllegroHeaders::ALLEGRO_ORDER_ID => 'ALLEGRO - numer zamówienia (ID operacji)',
        AllegroHeaders::ALLEGRO_PAYMENT_ID => 'ALLEGRO - numer płatności',
        AllegroHeaders::ORDER_ID => 'ID zlecenia - (numer zlecenia)',
        AllegroHeaders::PROMISE_PAYMENTS_SUM => 'ALLEGRO - wartości wpłat zamówienia',
        AllegroHeaders::REFUND_ID => 'ALLEGRO - ID zwrotów zamówienia',
        AllegroHeaders::REFUNDED => 'ALLEGRO - wartości zwrotów zamówienia',
    ],
    PaymentsHeader::class => [
        PaymentsHeader::ORDER_ID => 'Numer zlecenia',
        PaymentsHeader::PAYMENT_SUM => 'Suma płatności klienta',
    ],
    SheetNames::class => [
        SheetNames::ORDER_DATA => 'Zlecenia',
        SheetNames::ALLEGRO_PAYMENTS => 'Wpłaty Allegro',
        SheetNames::CLIENT_PAYMENTS => 'Wpłaty klienta',
    ]
];
