<?php

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;

return [
    DelivererRulesColumnNameEnum::class => [
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_ADDITIONAL_SERVICE => 'Kanał wpłaty',
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_DEPOSIT_VALUE => 'ALLEGRO wartość wpłaty',
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_FORM_ID => 'ALLEGRO numer zamówienia',
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_OPERATION_DATE => 'ALLEGRO data operacji',
        DelivererRulesColumnNameEnum::ORDER_REFUND_ID => 'ALLEGRO ID zwrotu',
        DelivererRulesColumnNameEnum::ORDER_PACKAGES_LETTER_NUMBER => 'Numer listu przewozowego',
        DelivererRulesColumnNameEnum::ORDER_PACKAGES_REAL_COST_FOR_COMPANY => 'Rzeczywiste koszty brutto transportu przesyłki',
        DelivererRulesColumnNameEnum::ORDER_PACKAGES_SERVICE_COURIER_NAME => 'Symbol firmy obsługującej przesyłkę',
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_COMMISSION => 'ALLEGRO wartość prowizji od sprzedaży towaru',
    ],
];
