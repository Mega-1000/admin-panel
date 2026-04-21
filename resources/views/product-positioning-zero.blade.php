@php
    $maxNumberOfSquares = ($productPositioningDTO->IKRPDOHNRWWRIZBRWWOG * $productPositioningDTO->IPHWOZPS + $productPositioningDTO->IOHWRRWROZWRWWOG);
    $borderRadius = false;
@endphp

@if($maxNumberOfSquares > 0)
<table style="align-self: flex-start;">
    <thead></thead>
    <tbody>
    @while($maxNumberOfSquares > 0)
        <tr>
            @if($productPositioningDTO->IPHWOZPS - 1 <= 0) @break @endif
            @for ($j = 0; $j <= $productPositioningDTO->IPHWOZPS - 1; $j++)
                @if($maxNumberOfSquares <= 0)
                    <td style="padding: 10px; border: 1px black solid; border-radius: 100%;"></td>
                    @php($borderRadius = true)
                        @break
                        @endif
                        @php($maxNumberOfSquares--)
                        @endfor
        </tr>
    @endwhile
    </tbody>
</table>
@endif
