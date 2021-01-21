<?php 

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ProductStockLogActionEnum extends Enum
{
    const DELETE = 'DELETE';
    const ADD = 'ADD';
}
