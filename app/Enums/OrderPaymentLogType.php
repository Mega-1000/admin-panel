<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OrderPaymentLogType extends Enum
{
    const OORDER_PAYMENT = 'ORDER_PAYMENT';
    const ClientPayment = 'CLIENT_PAYMENT';
    const ReturnPayment = 'RETURN_PAYMENT';
}
