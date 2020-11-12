<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OrderPaymentLogType extends Enum
{
    const OrderPayment = 'ORDER_PAYMENT';
    const ClientPayment = 'CLIENT_PAYMENT';
}
