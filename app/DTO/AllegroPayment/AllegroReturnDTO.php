<?php

namespace App\DTO\AllegroPayment;

class AllegroReturnDTO
{
    /**
     * @param string $paymentId
     * @param string $reason
     * @param AllegroReturnItemDTO[] $lineItems
     */
    public function __construct(
        public readonly string $paymentId,
        public readonly string $reason,
        public readonly array $lineItems,
    ) {}
}
