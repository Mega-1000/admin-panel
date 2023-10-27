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
        public string $paymentId,
        public mixed $reason = 'zwrot',
        public array $lineItems,
    ) {}

    public function toAllegroRefundArray(): array
    {
        if (empty($this->reason)) {
            $this->reason = 'zwrot';
        }
        return [
            'payment' => [
                'id' => $this->paymentId
            ],
            'reason' => $this->reason,
            'lineItems' => array_map(function (AllegroReturnItemDTO $item) {
                return $item->toAllegroRefundArray();
            }, $this->lineItems),
            'message' => 'zwrot'
        ];
    }
}
