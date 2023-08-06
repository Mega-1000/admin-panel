<?php

namespace App\DTO\AllegroPayment;

readonly class AllegroReturnDTO
{
    /**
     * @param string $paymentId
     * @param string $reason
     * @param AllegroReturnItemDTO[] $lineItems
     */
    public function __construct(
        public string $paymentId,
        public string $reason,
        public array $lineItems,
    ) {}

    public function toAllegroRefundArray(): array 
    {
        return [
            'payment' => [
                'id' => $this->paymentId
            ],
            'reason' => $this->reason,
            'lineItems' => array_map(function (AllegroReturnItemDTO $item) {
                return $item->toAllegroRefundArray();
            }, $this->lineItems)
        ];
    }
}
