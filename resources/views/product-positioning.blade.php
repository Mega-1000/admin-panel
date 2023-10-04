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
        <span class="bold">
            IKWJZWOG: {{ $productPositioningDTO->getQuantityOfCompleteLayersOfGlobalUnitsInGlobalUnit() }}
        </span>
        <span class="bold">
            IPJZNRWWOG: {{ $productPositioningDTO->getQuantityOfCompleteGlobalUnitsInStartedLayer() }}
        </span>
        <span>
            IJHWROZNRWZWJG: {{ $productPositioningDTO->getQuantityOfTradeItemsInStartedGlobalUnitInStartedLayer() }}
        </span>
        <span>
            PPROZPDWRWOG: {{ $productPositioningDTO->getQuantityOfCompleteRowsOfGlobalUnitsInStartedLayer() }}
        </span>
        <span class="bold">
            IOZWRRNRWWOG: {{ $productPositioningDTO->getQuantityOfGlobalUnitsInStartedRowInStartedLayer() }}
        </span>
        <span class="bold">
            IKRPDOHWOOZNRWWOG: {{ $productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInStartedGlobalUnit() }}
        </span>
        <span class="bold">
            IOHWRRROZWRWOG: {{ $productPositioningDTO->getQuantityOfTradeItemsInStartedRowInStartedLayerInStartedGlobalUnit() }}
        </span>
    @else
        <span>
            IJHNPWWOZ: {{ $productPositioningDTO->getQuantityOfTradeItemsInCompleteLayerInLargestUnit() }}
        </span>
        <span class="bold">
            IKWTWJHWOZ: {{ $productPositioningDTO->getQuantityOfCompleteLayersOfTradeItemsInLargestUnitInGlobalUnit() }}
        </span>
        <span class="bold">
            IJHNOWWROZ: {{ $productPositioningDTO->getQuantityOfTradeItemsInStartedLayerInLargestUnit() }}
        </span>
        <span class="bold">
            IPROHPDWOWWOZ: {{ $productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInLargestUnit() }}
        </span>
        <span class="bold">
            IOHWRRNOWWOZ: {{ $productPositioningDTO->getQuantityOfTradeItemsInStartedRowInStartedLayerInLargestUnit() }}
        </span>
    @endif
</div>
