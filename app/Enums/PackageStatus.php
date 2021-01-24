<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PackageStatus extends Enum
{
    const DELIVERED = 'DELIVERED';
    const SENDING = 'SENDING';
    const WAITING_FOR_SENDING = 'WAITING_FOR_SENDING';
}
