<?php

namespace App\DTO\AllegroBilling;

final class ImportAllegroBillingDTO
{
    public function __construct(
        protected ?string $data,
        protected ?string $nazwaOferty,
        protected ?string $identyfikatorOferty,
        protected ?string $typOperacji,
        protected ?string $uznania,
        protected ?string $obciazenia,
        protected ?string $saldo,
        protected ?string $szczegolyOperacji,
    ) {}

    /**
     * Get the value of data
     *
     * @return ?string
     */
    public function getDate(): ?string
    {
        return $this->data;
    }

    /**
     * Get the value of offer name
     *
     * @return ?string
     */
    public function getOfferName(): ?string
    {
        return $this->nazwaOferty;
    }

    /**
     * Get the value of offer identifier
     *
     * @return ?string
     */
    public function getOfferIdentifier(): ?string
    {
        return $this->identyfikatorOferty;
    }

    /**
     * Get the value of operation type
     *
     * @return ?string
     */
    public function getOperationType(): ?string
    {
        return $this->typOperacji;
    }

    /**
     * Get the value of credits
     *
     * @return ?string
     */
    public function getCredits(): ?string
    {
        return $this->uznania;
    }

    /**
     * Get the value of charges
     *
     * @return ?float
     */
    public function getCharges(): ?float
    {
        return (float)$this->obciazenia;
    }

    /**
     * Get the value of balance
     *
     * @return ?string
     */
    public function getBalance(): ?string
    {
        return $this->saldo;
    }

    /**
     * Get the value of operation details
     *
     * @return ?string
     */
    public function getOperationDetails(): ?string
    {
        return $this->szczegolyOperacji;
    }
}
