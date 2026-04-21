<?php

namespace App\DTO;

use App\DTO\PayInImport\BankPayInDTO;

class PayInDTO
{
    public function __construct(
        public ?int $orderId,
        public BankPayInDTO $data,
        public ?string $message
    ) {}
}
