<?php

namespace App\Http\Controllers;
readonly class CreateTWSOOrdersDTO
{
    public function __construct(
        protected ?string $warehousesSymbols = null,
        protected string $clientEmail,
        protected float $purchaseValue,
        protected ?string $consultantDescription,
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            warehousesSymbols: $request['warehousesSymbols'] ?? null,
            clientEmail:  $request['client_email'],
            purchaseValue: $request['purchase_value'],
            consultantDescription: $request['consultant_description'],
        );
    }

    public function getClientEmail(): string
    {
        return $this->clientEmail;
    }

    public function getWarehouseSymbol(): string
    {
        return $this->warehousesSymbols;
    }

    public function getPurchaseValue(): float
    {
        return $this->purchaseValue;
    }

    public function getConsultantDescription(): ?string
    {
        return $this->consultantDescription;
    }
}
