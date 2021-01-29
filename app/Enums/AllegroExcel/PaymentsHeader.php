<?php
declare(strict_types=1);

namespace App\Enums\AllegroExcel;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class PaymentsHeader extends Enum implements LocalizedEnum
{
    const ORDER_ID = 'order_id';
    const PAYMENT_SUM = 'payment_sum';
}
