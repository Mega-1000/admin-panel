<?php

namespace App\DTO\AllegroBilling;

final class ImportAllegroBillingDTO
{
    public function __construct(
        protected ?string $date,
        protected ?string $offerName,
        protected ?string $offerId,
        protected ?string $operationType,
        protected ?string $incomeAmount,
        protected ?string $outcomeAmount,
        protected ?string $balance,
        protected ?string $operationDetails,
    )
    {
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function getOfferName(): ?string
    {
        return $this->offerName;
    }

    public function getOfferIdentifier(): ?string
    {
        return $this->offerId;
    }

    public function getOperationType(): ?string
    {
        return $this->operationType;
    }

    public function getCredits(): ?string
    {
        return $this->incomeAmount;
    }

    public function getCharges(): ?float
    {
        return (float)str_replace(',', '.', $this->outcomeAmount);
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function getOperationDetails(): ?string
    {
        return $this->operationDetails;
    }
}
