<?php

namespace App\Enums\CourierStatus;

use BenSampo\Enum\Enum;

final class GlsPackageStatus extends Enum
{
    const DELIVERED = 'Paczka doreczona';
    const SENDING = 'Paczka zarejestrowana w filii GLS';
    const WAITING_FOR_SENDING = 'Nadawca nadal numer paczce';
}
