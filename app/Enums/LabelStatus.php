<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class LabelStatus extends Enum
{
    const Active = 'ACTIVE';
    const Pending =   'PENDING';
}
