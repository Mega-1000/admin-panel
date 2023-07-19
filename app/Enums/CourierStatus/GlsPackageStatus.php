<?php

namespace App\Enums\CourierStatus;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @Class GlsPackageStatus
 *
 * Paczka odebrana od Nadawcy
 * Paczka została przekazana GLS
 */
final class GlsPackageStatus extends Enum implements LocalizedEnum
{
    public const PREADVICE = 'PREADVICE';     // Dane paczki
    public const INTRANSIT = 'INTRANSIT';     // W tranzycie // W tranzycie
    public const INWAREHOUSE = 'INWAREHOUSE'; // Paczka zarejestrowana w filii GLS // Paczka zarejestrowana w filii GLS
    public const INDELIVERY = 'INDELIVERY';   // W doręczeniu // Paczka w doręczeniu
    public const DELIVERED = 'DELIVERED';     // Paczka doręczona
}
