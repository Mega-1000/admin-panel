<?php

namespace App\DTO\AllegroPayment;

use App\Enums\AllegroReturnItemTypeEnum;

readonly class AllegroReturnItemDTO
{
    public function __construct(
        public string $id,
        public AllegroReturnItemTypeEnum $type,
        public ?int $quantity = null,
        public ?float $amount = null,
        public ?string $currency = "PLN",
    ) {}

    public function toAllegroRefundArray(): array 
    {
        if ($this->type->is(AllegroReturnItemTypeEnum::AMOUNT)) {
            return [
                'id' => $this->id,
                'type' => $this->type->value,
                'value' => [
                    'amount' => $this->amount,
                    'currency' => $this->currency,
                ],
            ];
        }

        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'quantity' => $this->quantity,
        ];
    }
}
