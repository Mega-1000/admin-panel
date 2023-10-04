<div>
    @if(!$productPositioningDTO->getIsZero())
        <span>
            IJHWOZ: {{ $productPositioningDTO->getQuantityOfTradeItemsInLargestUnit() }}
        </span>
        <span>
            IJHWOG: {{ $productPositioningDTO->getQuantityOfTradeItemsInGlobalUnit() }}
        </span>
        <span>
            IJZNKWWOG: {{ $productPositioningDTO->getQuantityOfGlobalUnitsInCompleteLayer() }}
        </span>
        <span>
            IKWJZWOG: {{ $productPositioningDTO->getQuantityOfCompleteLayersOfGlobalUnitsInGlobalUnit() }}
        </span>
        <span>
            IPJZNRWWOG: {{ $productPositioningDTO->getQuantityOfCompleteGlobalUnitsInStartedLayer() }}
        </span>
        <span>
            IJHWROZNRWZWJG: {{ $productPositioningDTO->getQuantityOfTradeItemsInStartedGlobalUnitInStartedLayer() }}
        </span>
        <span>
            PPROZPDWRWOG: {{ $productPositioningDTO->getQuantityOfCompleteRowsOfGlobalUnitsInStartedLayer() }}
        </span>
        <span>
            IOZWRRNRWWOG: {{ $productPositioningDTO->getQuantityOfGlobalUnitsInStartedRowInStartedLayer() }}
        </span>
        <span>
            IKRPDOHWOOZNRWWOG: {{ $productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInStartedGlobalUnit() }}
        </span>
        <span>
            IOHWRRROZWRWOG: {{ $productPositioningDTO->getQuantityOfTradeItemsInStartedRowInStartedLayerInStartedGlobalUnit() }}
        </span>
    @else
        <span>
            IJHNPWWOZ: {{ $productPositioningDTO->getQuantityOfTradeItemsInCompleteLayerInLargestUnit() }}
        </span>
        <span>
            IKWTWJHWOZ: {{ $productPositioningDTO->getQuantityOfCompleteLayersOfTradeItemsInLargestUnitInGlobalUnit() }}
        </span>
        <span>
            IJHNOWWROZ: {{ $productPositioningDTO->getQuantityOfTradeItemsInStartedLayerInLargestUnit() }}
        </span>
        <span>
            IPROHPDWOWWOZ: {{ $productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInLargestUnit() }}
        </span>
        <span>
            IOHWRRNOWWOZ: {{ $productPositioningDTO->getQuantityOfTradeItemsInStartedRowInStartedLayerInLargestUnit() }}
        </span>
    @endif
</div>
