<?php

namespace App\DTO\ProductPositioning;

use App\Traits\ArrayOperations;

/**
 * IWK - ilosc warst kompletnych
 * IJDNW - ilosc jednsotek dotyczacych na warstwie
 * IJHWOZ - ilosc jednostek handlowych w opakowniu zbiorczym
 * IJZNOWK - ilosc jednostek zbiorczych na ostatniej warstwie kompletnych
 * IJHNOWWOZR - ilosc jednostek handlowych na ostatniej warstwie w opakowaniu zbiorczym rozpoczetym
 */
readonly final class ProductPositioningDTO
{
    use ArrayOperations;

    public function __construct(
        private int $numberOfCompleteLayers,
        private int $numberOfItemsPerLayer,
        private int $numberOfCommercialUnitsInBulk,
        private int $numberOfBulkUnitsInLastLayer,
        private int $numberOfCommercialUnitsInLastLayerOfStartedBulk,
    ) {}

    public static function fromAcronymsArray(array $data): self
    {
        return new self(
            numberOfCompleteLayers: $data['IWK'],
            numberOfItemsPerLayer: $data['IJDNW'],
            numberOfCommercialUnitsInBulk: $data['IJHWOZ'],
            numberOfBulkUnitsInLastLayer: $data['IJZNOWK'],
            numberOfCommercialUnitsInLastLayerOfStartedBulk: $data['IJHNOWWOZR'],
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

    public function getNumberOfBulkUnitsInLastLayer(): int
    {
        return $this->numberOfBulkUnitsInLastLayer;
    }

    public function getNumberOfCommercialUnitsInLastLayerOfStartedBulk(): int
    {
        return $this->numberOfCommercialUnitsInLastLayerOfStartedBulk;
    }
}
