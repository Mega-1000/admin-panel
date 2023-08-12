<?php

namespace App\DTO;

use App\Entities\Order;

class PayInDTO
{
    public function __construct(
        public ?int $orderId,
        public array $data,
        public ?string $message
    ) {}
}
