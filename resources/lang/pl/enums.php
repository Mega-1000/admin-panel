<?php

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Enums\CourierStatus\DpdPackageStatus;
use App\Enums\CourierStatus\GlsPackageStatus;

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
    ]
];
