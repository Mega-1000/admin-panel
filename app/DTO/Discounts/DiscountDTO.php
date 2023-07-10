<?php

namespace App\DTO\Discounts;

readonly class DiscountDTO
{
    public function __construct(
        private ?string $description,
        private float $new_amount,
        private float $old_amount,
        private int $product_id
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            $request['description'],
            $request['new_amount'],
            $request['old_amount'],
            $request['product_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'new_amount' => $this->new_amount,
            'old_amount' => $this->old_amount,
            'product_id' => $this->product_id,
        ];
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getNewAmount(): float
    {
        return $this->new_amount;
    }
}
