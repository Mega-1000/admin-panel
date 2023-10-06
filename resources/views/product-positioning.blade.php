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

        <table>
            <tbody>
            @for ($i = 0; $i < $productPositioningDTO->getQuantityOfCompleteRowsOfTradeItemsInStartedLayerInStartedGlobalUnit(); $i++)
                <tr>
                    @for ($j = 0; $j < $productPositioningDTO->getProduct()->packing->number_of_trade_units_in_package_width - 1; $j++)
                        <td style="padding: 10px; border: 1px black solid;"></td>
                    @endfor
                </tr>
            @endfor
            </tbody>
        </table>

        <table>
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
