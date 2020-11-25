<tr>
    <td style="width: 100px;display:inline-block"><img
                src="{!!  $item->getImageUrl() !!}"
                alt="{{ $item->name }}"
                style="width:70px"/></td>
    <td style="width: 300px;display:inline-block"><span
                style="font-size:14px; font-weight:bold;">{{ $item->name }}</span><br/>symbol: {{ $item->symbol }}
        <br/>Ilość: <span style="font-size: 1.5em; font-weight: bold;">{{ $quantity }} {{ $item->packing->calciation_unit }}</span><br/></td>
    @if(isset($showPosition) && $showPosition)
        @if(count($item->getPositions()))
            @foreach($item->getPositions() as $position)
                <td style="width: 100px;display:inline-block">
                    Alejka: {{ $position->lane }} <br/>
                    Regał: {{ $position->bookstand }} </br>
                    Półka: {{ $position->shelf }} </br>
                    Pozycja: {{ $position->position }} </br>
                    Ilość: {{ $position->position_quantity }} </br>
                </td>
            @endforeach
        @endif
    @endif
</tr>
