<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OrderPaymentStatus extends Enum
{
    const PENDING = 'PENDING';
    const ACTIVE = 'ACTIVE';
}
