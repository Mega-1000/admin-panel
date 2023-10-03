<?php

namespace App\DTO\ProductPositioning;

use App\Traits\ArrayOperations;

/**
 *
 * IJHWOZ - ilosc jednostek handlowych w opakowniu zbiorczym
 * IJHWOG - ilosc jednostek handlowych w opakowaniu globalnym
 *
 *
 * IJZNKWWOG - ilosc jednostek zbiorczych na kompletnej warstwie w opakowani globalnym
 * IKWJZWOG - ilosc kopletnych warstw jednostek zbiorczych w opakowaniu globalnym
 * IPJZNRWWOG - ilosc pelnych jednostek zbiorczych na rozpoczetej warstwie w opakowaniu globalnym
 * IJHWROZNRWZWJG - ilosc jednostek handlowych w rozpoczetym opakowaniu zbiorczym na rozpoczetej warstwie zbiorczej w jednostce globalnej
 *
 *
 * PPROZPDWRWOG - ilosc pelnych rzedow opakowan zbiorczych po dlugosci w rozpoczetej warstwie w opakowaniu globalnym
 * IOZWRRNRWWOG - ilosc opakowan zbiorczych w rozpoczetym rzedzie na rozpoczeteje warstwie w opakowaniu globalnym
 *
 *
 * IJHNPWWOZ - ilosc jednostek handlowych na pelnej warstwie w opakowaniu zbiorczym
 * IKWTWJHWOZ - ilosc kompletnych warst towarow w jednostkach handlowych w opakowaniu zbiorczym
 * IJHNOWWROZ  - ilosc jednostek handlowych na ostatniej warstwie w rozpoczetym opakowaniu zbioczym
 *
 *
 * IPROHPDWOWWOZ - ilosc pelnych rzedow opakowan handlowych po dlugosci  w ostatniej warstwie w opakowaniu zbiorczym
 * IOHWRRNOWWOZ - ilosc opakowan handlowych w rozpoczetym rzedzie na ostatniej warstwie w opakowaniu zbiorcczym
 */
readonly final class ProductPositioningDTO
{
    use ArrayOperations;

    public function __construct(
        private float $quantityOfTradeItemsInLargestUnit, // IJHWOZ
        private float $quantityOfTradeItemsInGlobalUnit, // IJHWOG
        private float $quantityOfGlobalUnitsInCompleteLayer, // IJZNKWWOG
        private float $quantityOfCompleteLayersOfGlobalUnitsInGlobalUnit, // IKWJZWOG
        private float $quantityOfCompleteGlobalUnitsInStartedLayer, // IPJZNRWWOG
        private float $quantityOfTradeItemsInStartedGlobalUnitInStartedLayer, // IJHWROZNRWZWJG
        private float $quantityOfCompleteRowsOfGlobalUnitsInStartedLayer, // PPROZPDWRWOG
        private float $quantityOfGlobalUnitsInStartedRowInStartedLayer, // IOZWRRNRWWOG
        private float $quantityOfTradeItemsInCompleteLayerInLargestUnit, // IJHNPWWOZ
        private float $quantityOfCompleteLayersOfTradeItemsInLargestUnitInGlobalUnit, // IKWTWJHWOZ
        private float $quantityOfTradeItemsInStartedLayerInLargestUnit, // IJHNOWWROZ
        private float $quantityOfCompleteRowsOfTradeItemsInStartedLayerInLargestUnit, // IPROHPDWOWWOZ
        private float $quantityOfTradeItemsInStartedRowInStartedLayerInLargestUnit, // IOHWRRNOWWOZ

    ) {}

    public static function fromAcronymsArray(array $data): self
    {
        return new self(
            $data['IJHWOZ'],
            $data['IJHWOG'],
            $data['IJZNKWWOG'],
            $data['IKWJZWOG'],
            $data['IPJZNRWWOG'],
            $data['IJHWROZNRWZWJG'],
            $data['PPROZPDWRWOG'],
            $data['IOZWRRNRWWOG'],
            $data['IJHNPWWOZ'],
            $data['IKWTWJHWOZ'],
            $data['IJHNOWWROZ'],
            $data['IPROHPDWOWWOZ'],
            $data['IOHWRRNOWWOZ'],
        );
    }

    // getters
    public function getQuantityOfTradeItemsInLargestUnit(): float
    {
        return $this->quantityOfTradeItemsInLargestUnit;
    }

    public function getQuantityOfTradeItemsInGlobalUnit(): float
    {
        return $this->quantityOfTradeItemsInGlobalUnit;
    }

    public function getQuantityOfGlobalUnitsInCompleteLayer(): float
    {
        return $this->quantityOfGlobalUnitsInCompleteLayer;
    }

    public function getQuantityOfCompleteLayersOfGlobalUnitsInGlobalUnit(): float
    {
        return $this->quantityOfCompleteLayersOfGlobalUnitsInGlobalUnit;
    }

    public function getQuantityOfCompleteGlobalUnitsInStartedLayer(): float
    {
        return $this->quantityOfCompleteGlobalUnitsInStartedLayer;
    }

    public function getQuantityOfTradeItemsInStartedGlobalUnitInStartedLayer(): float
    {
        return $this->quantityOfTradeItemsInStartedGlobalUnitInStartedLayer;
    }

    public function getQuantityOfCompleteRowsOfGlobalUnitsInStartedLayer(): float
    {
        return $this->quantityOfCompleteRowsOfGlobalUnitsInStartedLayer;
    }

    public function getQuantityOfGlobalUnitsInStartedRowInStartedLayer(): float
    {
        return $this->quantityOfGlobalUnitsInStartedRowInStartedLayer;
    }

    public function getQuantityOfTradeItemsInCompleteLayerInLargestUnit(): float
    {
        return $this->quantityOfTradeItemsInCompleteLayerInLargestUnit;
    }

    public function getQuantityOfCompleteLayersOfTradeItemsInLargestUnitInGlobalUnit(): float
    {
        return $this->quantityOfCompleteLayersOfTradeItemsInLargestUnitInGlobalUnit;
    }

    public function getQuantityOfTradeItemsInStartedLayerInLargestUnit(): float
    {
        return $this->quantityOfTradeItemsInStartedLayerInLargestUnit;
    }

    public function getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInLargestUnit(): float
    {
        return $this->quantityOfCompleteRowsOfTradeItemsInStartedLayerInLargestUnit;
    }

    public function getQuantityOfTradeItemsInStartedRowInStartedLayerInLargestUnit(): float
    {
        return $this->quantityOfTradeItemsInStartedRowInStartedLayerInLargestUnit;
    }
}
