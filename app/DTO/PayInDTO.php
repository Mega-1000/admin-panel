<?php

namespace App\DTO;

use App\Entities\Order;

class PayInDTO
{
    public function __construct(
        public string|int $returnedValue,
        public array $data
    )
    {
    }
}
