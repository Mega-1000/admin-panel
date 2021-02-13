<?php
declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ProductStockError extends Enum
{
    const POSITION = 'position';
    const QUANTITY = 'quantity';
    const EXISTS = 'exists';
}
