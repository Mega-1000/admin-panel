<?php
declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PackageStatus extends Enum
{
    const DELIVERED = 'DELIVERED';
    const SENDING = 'SENDING';
    const WAITING_FOR_SENDING = 'WAITING_FOR_SENDING';
    const CANCELLED = 'CANCELLED';
    const WAITING_FOR_CANCELLED = 'WAITING_FOR_CANCELLED';
    const NEW = 'NEW';
    const REJECT_CANCELLED = 'REJECT_CANCELLED';
}
