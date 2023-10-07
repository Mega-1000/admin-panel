<div style="width: 1000px;">
    <div style="display: flex; flex-direction: row;">
        @if($productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInStartedGlobalUnit() != 0)
            <div style="flex: none;">
                @for($i = $productPositioningDTO->getQuantityOfCompleteLayersOfGlobalUnitsInGlobalUnit() - 1; $i >= 0; $i--)
                    <div style="font-weight: bold; font-size: larger">
                        --
                    </div>
                    <br>
                @endfor
            </div>
        @endif

        @php
            $maxNumberOfSquares = $productPositioningDTO->getQuantityOfCompleteGlobalUnitsInStartedLayer();
            $borderRadius = $maxNumberOfSquares != 0 ? false : true;
        @endphp

        <table style="flex: none;">
            <thead></thead>
            <tbody>
            @while($maxNumberOfSquares > 0)
                <tr>
                    @for ($j = 0; $j <= $productPositioningDTO->getProduct()->packing->number_of_trade_units_in_width_in_global_package - 1; $j++)
                        @if($maxNumberOfSquares <= 0)
                            <td style="padding: 10px; border: 1px black solid; border-radius: 100%; flex: none;"></td>
                            @php($borderRadius = true)
                            @break
                        @endif

                        <td style="padding: 10px; border: 1px black solid; flex: none;"></td>
                        @php($maxNumberOfSquares--)
                    @endfor
                </tr>
            @endwhile
            @if(!$borderRadius)
                <tr>
                    <td style="padding: 10px; border: 1px black solid; border-radius: 100%; flex: none;"></td>
                </tr>
            @endif
            </tbody>
        </table>

        <table style="margin-top: 10px; flex: none;">
            <tbody>
            @for ($i = 0; $i < $productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInStartedGlobalUnit(); $i++)
                <tr>
                    @for ($j = 0; $j < $productPositioningDTO->getProduct()->packing->number_of_trade_units_in_package_width; $j++)
                        <td style="padding: 10px; border: 1px black solid; flex: none;"></td>
                    @endfor
                </tr>
            @endfor
            </tbody>
        </table>
    </div>
</div>
