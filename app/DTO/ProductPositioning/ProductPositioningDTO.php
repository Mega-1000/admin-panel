<?php

namespace App\DTO\ProductPositioning;

use App\Traits\ArrayOperations;

/**
 * IWK - ilosc warst kompletnych
 * IJHWOZ - ilosc jednostek handlowych w opakowniu zbiorczym
 * IJZNOWK - ilosc jednostek zbiorczych na ostatniej warstwie kompletnych
 * IJHWROZ - ilosc jednostek handlowych na ostatniej warstwie w opakowaniu zbiorczym rozpoczetym
 */
readonly final class ProductPositioningDTO
{
    use ArrayOperations;

    public function __construct(
        private int $numberOfCompleteLayers,
        private int $numberOfCommercialUnitsInBulk,
        private int $numberOfCommercialUnitsInBulkOnLastCompleteLayer,
        private int $numberOfCommercialUnitsInBulkOnLastIncompleteLayer,
    ) {}

    public static function fromAcronymsArray(array $data): self
    {
        return new self(
            $data['IWK'],
            $data['IJHWOZ'],
            $data['IJZNOWK'],
            $data['IJHWROZ'],
        );
    }

    public function getNumberOfCompleteLayers(): int
    {
        return $this->numberOfCompleteLayers;
    }

    public function getNumberOfItemsPerLayer(): int
    {
        return $this->numberOfItemsPerLayer;
    }

    public function getNumberOfCommercialUnitsInBulk(): int
    {
        return $this->numberOfCommercialUnitsInBulk;
    }

    public function getNumberOfCommercialUnitsInBulkOnLastCompleteLayer(): int
    {
        return $this->numberOfCommercialUnitsInBulkOnLastCompleteLayer;
    }

    public function getNumberOfCommercialUnitsInBulkOnLastIncompleteLayer(): int
    {
        return $this->numberOfCommercialUnitsInBulkOnLastIncompleteLayer;
    }
}
