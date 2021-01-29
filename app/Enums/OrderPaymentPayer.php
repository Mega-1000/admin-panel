<?php
declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OrderPaymentPayer extends Enum
{
    const WAREHOUSE = 'WAREHOUSE';
    const CLIENT = 'CLIENT';
}
