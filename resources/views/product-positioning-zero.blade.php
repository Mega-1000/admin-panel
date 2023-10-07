
<div>
    <table>
        <tbody>
        <tr>
            <td>
                @if($productPositioningDTO->getQuantityOfCompleteLayersOfTradeItemsInLargestUnitInGlobalUnit() != 0)
                    <div style="align-self: flex-start;">
                        @for($i = $productPositioningDTO->getQuantityOfCompleteLayersOfGlobalUnitsInGlobalUnit() - 1; $i >= 0; $i--)
                            <div style="font-weight: bold; font-size: larger">
                                --
                            </div>
                            <br>
                        @endfor
                    </div>
                @endif

                @php
                    $maxNumberOfSquares =
                        $productPositioningDTO
                        ->getQuantityOfCompleteLayersOfTradeItemsInLargestUnitInGlobalUnit()
                        +
                        $productPositioningDTO
                        ->getQuantityOfTradeItemsInStartedLayerInLargestUnit();
                @endphp
            </td>
            <td>
                <table style="align-self: flex-start;">
                    <thead></thead>
                    <tbody>
                    @while($maxNumberOfSquares > 0)
                        <tr>
                            @for ($j = 0; $j <= $productPositioningDTO->getProduct()->packing->number_of_trade_units_in_package_width; $j++)
                                <td style="padding: 10px; border: 1px black solid;"></td>
                                @php
                                    $maxNumberOfSquares--
                                @endphp
                            @endfor
                        </tr>
                    @endwhile
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
