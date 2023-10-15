<table border="1">
    <thead>
    <tr>
        <th style="width: 65px"></th>
        <th>GLO</th>
        <th>HAN</th>
        <th>P1</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>il p warstw</td>
        <td>{{ $productPositioningDTO->IKWJZWOG }}</td>
        <td>{{ $productPositioningDTO->IPWJHWROZWOG }}</td>
        <td>{{ $productPositioningDTO->IKWW1WROZWRWWROG }}</td>
    </tr>
    <tr>
        <td>po szerok</td>
        <td>{{ $productPositioningDTO->IJZPSWOG }}</td>
        <td>{{ $productPositioningDTO->IPHWOZPS }}</td>
        <td>{{ $productPositioningDTO->IPHWOZPS }}</td>
    </tr>
    <tr>
        <td>po dlugo</td>
        <td>{{ $productPositioningDTO->IJZPDWOG }}</td>
        <td>{{ $productPositioningDTO->IPHWOZPD }}</td>
        <td>{{ $productPositioningDTO->IPHWOZPD }}</td>
    </tr>
    <tr>
        <td>pelne</td>
        <td>{{ $productPositioningDTO->IKROZPDWRWOG }}</td>
        <td>{{ $productPositioningDTO->IKRPDOHNRWWRIZBRWWOG }}</td>
        <td>{{ $productPositioningDTO->IKOP1WRWWW1WOG }}</td>
    </tr>
    <tr>
        <td>p. rozp.</td>
        <td>{{ $productPositioningDTO->IKOZWRRNRWWOG }}</td>
        <td>{{ $productPositioningDTO->IOHWRRWROZWRWWOG }}</td>
        <td>{{ $productPositioningDTO->IOHWROP1WRWWOG }}</td>
    </tr>
    </tbody>
</table>

<div style="display: flex; flex-direction: row;">
    @php
        $maxNumberOfSquares = ($productPositioningDTO->IKROZPDWRWOG * $productPositioningDTO->IJZPSWOG + $productPositioningDTO->IKOZWRRNRWWOG);
        $borderRadius = $maxNumberOfSquares == 0;
    @endphp

    <table style="align-self: flex-start;">
        <thead></thead>
        <tbody>
        @while($maxNumberOfSquares > 0)
            <tr>
                @for ($j = 0; $j <= $productPositioningDTO->IJZPSWOG - 1; $j++)
                    @if($maxNumberOfSquares <= 0)
                        <td style="padding: 10px; border: 1px black solid; border-radius: 100%;"></td>
                        @php($borderRadius = true)
                        @break
                    @endif

                    <td style="padding: 10px; border: 1px black solid;"></td>
                    @php($maxNumberOfSquares--)
                @endfor
            </tr>
        @endwhile

        @if(!$borderRadius)
            <tr>
                <td style="padding: 10px; border: 1px black solid; border-radius: 100%;"></td>
            </tr>
        @endif
        </tbody>
    </table>

    <div style="margin-right: 15px">
        @include('product-positioning-zero', ['productPositioningDTO' => $productPositioningDTO])
    </div>
</div>

IJHWOZ: {{ $productPositioningDTO->IJHWOZ }}<br>
IJHWOG: {{ $productPositioningDTO->IJHWOG }}<br>
IOHWOP1: {{ $productPositioningDTO->IOHWOP1 }}<br>
IJHNKWWOZ: {{ $productPositioningDTO->IJHNKWWOZ }}<br>
IJZNKWWOG: {{ $productPositioningDTO->IJZNKWWOG }}<br>

IWJNWPWOZ: {{ $productPositioningDTO->IWJNWPWOZ }}<br>
IPHWOZPD: {{ $productPositioningDTO->IPHWOZPD }}<br>
IPHWOZPS: {{ $productPositioningDTO->IPHWOZPS }}<br>
IJZPDWOG: {{ $productPositioningDTO->IJZPDWOG }}<br>
IJZPSWOG: {{ $productPositioningDTO->IJZPSWOG }}<br>

IOHKSPWZIP1NPWW1WOH: {{ $productPositioningDTO->IOHKSPWZIP1NPWW1WOH }}<br>
IKWJZWOG: {{ $productPositioningDTO->IKWJZWOG }}<br>
IPJZNRWWOG: {{ $productPositioningDTO->IPJZNRWWOG }}<br>
IJHWROZNRWZWJG: {{ $productPositioningDTO->IJHWROZNRWZWJG }}<br>
IKROZPDWRWOG: {{ $productPositioningDTO->IKROZPDWRWOG }}<br>

IKOZWRRNRWWOG: {{ $productPositioningDTO->IKOZWRRNRWWOG }}<br>
IPWJHWROZWOG: {{ $productPositioningDTO->IPWJHWROZWOG }}<br>
IKRPDOHNRWWRIZBRWWOG: {{ $productPositioningDTO->IKRPDOHNRWWRIZBRWWOG }}<br>
IOHWRRWROZWRWWOG: {{ $productPositioningDTO->IOHWRRWROZWRWWOG }}<br>
IKWW1WROZWRWWROG: {{ $productPositioningDTO->IKWW1WROZWRWWROG }}<br>


IKOZWRRNRWWOG
