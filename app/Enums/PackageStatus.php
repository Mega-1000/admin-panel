<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PackageStatus extends Enum
{
    const Delivered = 'DELIVERED';
    const Sending = 'SENDING';
    const WaitingForSending = 'WAITING_FOR_SENDING';
}
