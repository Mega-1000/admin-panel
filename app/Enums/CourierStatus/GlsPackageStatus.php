<?php
declare(strict_types=1);

namespace App\Enums\CourierStatus;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class GlsPackageStatus extends Enum implements LocalizedEnum
{
    public const DELIVERED = 'delivered';
    public const SENDING = 'sending';
    public const WAITING_FOR_SENDING = 'waiting_for_sending';
}
