<?php

namespace App\Enums\AllegroImport;

enum AllegroOperationTyeEnum
{
    const POSSIBLE_OPERATION_TYPES = [
        'Abonament podstawowy',
        'Abonament profesjonalny',
        'Korekta rachunku',
        'Opłata za kampanię Ads',
        'Opłata za wyróżnienie',
        'Jednostkowa opłata transakcyjna',
    ];
}
