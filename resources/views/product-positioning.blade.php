<div style="width: 1000px">
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

        <br>
        <br>
        <br>

    <div>
        <div>
            @for($i = $productPositioningDTO->getQuantityOfCompleteLayersOfGlobalUnitsInGlobalUnit() - 1; $i >= 0; $i--)
                <div style="font-weight: bold; font-size: larger">
                    --
                </div>
                <br>
            @endfor
        </div>

        <div style="display: flex" style="margin-left: 20px;">
            <div style="display: grid; margin-left: 15px; grid-template-columns: repeat({{ $productPositioningDTO->getProduct()->packing->number_of_trade_units_in_width_in_global_package }}, 1fr); grid-gap: 10px;">
                @for($i = $productPositioningDTO->getQuantityOfCompleteGlobalUnitsInStartedLayer() - 1; $i >= 0; $i--)
                    <div style="padding: 10px; border: 1px black solid"></div>
                @endfor

                @if(($productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInStartedGlobalUnit() * $productPositioningDTO->getProduct()->packing->number_of_trade_units_in_package_width + $productPositioningDTO->getQuantityOfTradeItemsInStartedRowInStartedLayerInStartedGlobalUnit()) - 1 >= 0)
                    <div style="padding: 10px; border: 1px black solid; border-radius: 100%"></div>
                @endif
            </div>
        </div>
        {{($productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInStartedGlobalUnit() * $productPositioningDTO->getProduct()->packing->number_of_trade_units_in_package_width + $productPositioningDTO->getQuantityOfTradeItemsInStartedRowInStartedLayerInStartedGlobalUnit()) - 1}}

        <div style="display: flex">
            <div style="display: grid; margin-left: 15px; grid-template-columns: repeat({{ $productPositioningDTO->getProduct()->packing->number_of_trade_units_in_package_width }}, 1fr); grid-gap: 10px;">
                @for($i = ($productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInStartedGlobalUnit() * $productPositioningDTO->getProduct()->packing->number_of_trade_units_in_package_width + $productPositioningDTO->getQuantityOfTradeItemsInStartedRowInStartedLayerInStartedGlobalUnit()) - 1; $i >= 0; $i--)
                    <div style="padding: 10px; border: 1px black solid"></div>
                @endfor
            </div>
        </div>
    </div>

</div>
