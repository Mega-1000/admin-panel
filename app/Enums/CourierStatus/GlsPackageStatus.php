<?php
declare(strict_types=1);

namespace App\Enums\CourierStatus;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class GlsPackageStatus extends Enum implements LocalizedEnum
{
    public const DELIVERED = 'DELIVERED';
    public const PREADVICE = 'PREADVICE';
    public const INTRANSIT = 'INTRANSIT';
    public const INWAREHOUSE = 'INWAREHOUSE';
    public const INDELIVERY = 'INDELIVERY';
}
