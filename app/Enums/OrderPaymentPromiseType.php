<?php
declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OrderPaymentPromiseType extends Enum
{
    const PROMISED = 1;
    const BOOKED = '';
}
