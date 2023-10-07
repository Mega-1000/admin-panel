<div style="width: 1000px">
    <div>
        @if($productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInStartedGlobalUnit() != 0)
            <div>
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
        @endphp

        <table>
            <thead>
            </thead>
            <tbody>
                @while($maxNumberOfSquares > 0)
                    <tr>
                        @for ($j = 0; $j <= $productPositioningDTO->getProduct()->packing->number_of_trade_units_in_width_in_global_package - 1; $j++)
                            @if($maxNumberOfSquares == 0)
                                <td style="padding: 10px; border: 1px black solid; border-radius: 100%"></td>
                                @break
                            @endif

                            @if($maxNumberOfSquares < 0)
                                @break
                            @endif
                            <td style="padding: 10px; border: 1px black solid;"></td>

                            @php($maxNumberOfSquares--)
                        @endfor
                    </tr>
                @endwhile
            </tbody>
        </table>

        <table style="margin-top: 10px">
            <tbody>
            @for ($i = 0; $i < $productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInStartedGlobalUnit(); $i++)
                <tr>
                    @for ($j = 0; $j < $productPositioningDTO->getProduct()->packing->number_of_trade_units_in_package_width; $j++)
                        <td style="padding: 10px; border: 1px black solid;"></td>
                    @endfor
                </tr>
            @endfor
            </tbody>
        </table>
    </div>
</div>
