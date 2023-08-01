<?php

namespace App\DTO\AllegroPayment;
use App\Enums\AllegroReturnItemTypeEnum;

class AllegroReturnItemDTO
{
    public function __construct(
        public readonly string $id,
        public readonly AllegroReturnItemTypeEnum $type,
        public readonly ?int $quantity = null,
        public readonly ?float $amount = null,
        public readonly ?string $currency = "PLN",
    ) {}
}
