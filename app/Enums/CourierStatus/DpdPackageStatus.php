<?php

namespace App\Enums\CourierStatus;

use BenSampo\Enum\Enum;

final class DpdPackageStatus extends Enum
{
    const DELIVERED = "Przesyłka doręczona";
    const SENDING = "Przesyłka odebrana przez Kuriera";
    const WAITING_FOR_SENDING = "Zarejestrowano dane przesyłki";
}
