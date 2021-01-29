<?php
declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class LabelEventName extends Enum
{
    const PAYMENT_RECEIVED = 'payment-received';
}
