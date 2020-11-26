<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OrderPaymentLogTypeEnum extends Enum
{
    const ORDER_PAYMENT = 'ORDER_PAYMENT';
    const CLIENT_PAYMENT = 'CLIENT_PAYMENT';
    const RETURN_PAYMENT = 'RETURN_PAYMENT';
}
