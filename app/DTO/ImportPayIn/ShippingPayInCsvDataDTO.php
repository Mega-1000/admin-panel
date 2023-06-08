<?php

namespace App\DTO\ImportPayIn;

readonly final class ShippingPayInCsvDataDTO
{
    public function __construct(
        protected string $nrFakturyDoKtorejDanyLpZostalPrzydzielony,
        protected string $wartoscPobrania,
    ) {}

    public function getNrFakturyDoKtorejDanyLpZostalPrzydzielony(): string
    {
        return $this->nrFakturyDoKtorejDanyLpZostalPrzydzielony;
    }

    public function getWartoscPobrania(): string
    {
        return $this->wartoscPobrania;
    }
}
