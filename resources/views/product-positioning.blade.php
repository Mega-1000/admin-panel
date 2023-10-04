<style>
    .bold {
        font-weight: bold;
    }
</style>

<div>
    @if(!$productPositioningDTO->isZero())
        <span>
            IJHWOZ: {{ $productPositioningDTO->getQuantityOfTradeItemsInLargestUnit() }}
        </span>
        <span>
            IJHWOG: {{ $productPositioningDTO->getQuantityOfTradeItemsInGlobalUnit() }}
        </span>
        <span>
            IJZNKWWOG: {{ $productPositioningDTO->getQuantityOfGlobalUnitsInCompleteLayer() }}
        </span>
        <span style="font-weight: bold">
            IKWJZWOG: {{ $productPositioningDTO->getQuantityOfCompleteLayersOfGlobalUnitsInGlobalUnit() }}
        </span>
        <span style="font-weight: bold">
            IPJZNRWWOG: {{ $productPositioningDTO->getQuantityOfCompleteGlobalUnitsInStartedLayer() }}
        </span>
        <span>
            IJHWROZNRWZWJG: {{ $productPositioningDTO->getQuantityOfTradeItemsInStartedGlobalUnitInStartedLayer() }}
        </span>
        <span>
            PPROZPDWRWOG: {{ $productPositioningDTO->getQuantityOfCompleteRowsOfGlobalUnitsInStartedLayer() }}
        </span>
        <span style="font-weight: bold">
            IOZWRRNRWWOG: {{ $productPositioningDTO->getQuantityOfGlobalUnitsInStartedRowInStartedLayer() }}
        </span>
        <span style="font-weight: bold">
            IKRPDOHWOOZNRWWOG: {{ $productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInStartedGlobalUnit() }}
        </span>
        <span style="font-weight: bold">
            IOHWRRROZWRWOG: {{ $productPositioningDTO->getQuantityOfTradeItemsInStartedRowInStartedLayerInStartedGlobalUnit() }}
        </span>
    @else
        <span>
            IJHNPWWOZ: {{ $productPositioningDTO->getQuantityOfTradeItemsInCompleteLayerInLargestUnit() }}
        </span>
        <span style="font-weight: bold">
            IKWTWJHWOZ: {{ $productPositioningDTO->getQuantityOfCompleteLayersOfTradeItemsInLargestUnitInGlobalUnit() }}
        </span>
        <span style="font-weight: bold">
            IJHNOWWROZ: {{ $productPositioningDTO->getQuantityOfTradeItemsInStartedLayerInLargestUnit() }}
        </span>
        <span style="font-weight: bold">
            IPROHPDWOWWOZ: {{ $productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInLargestUnit() }}
        </span>
        <span style="font-weight: bold">
            IOHWRRNOWWOZ: {{ $productPositioningDTO->getQuantityOfTradeItemsInStartedRowInStartedLayerInLargestUnit() }}
        </span>
    @endif
</div>
