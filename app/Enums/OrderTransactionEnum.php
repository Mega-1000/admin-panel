<?php

namespace App\Enums;

enum OrderTransactionEnum {
    const CREATED_BY_BANK = 'bank';
    const CREATED_MANUALLY = 'manually';
    const CREATED_BY_ALLEGRO = 'allegro';
    const CREATED_BY_SHIPPING_TRANSACTION = 'shipping';
}
