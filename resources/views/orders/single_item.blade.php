<tr>
    <td style="width:10%"><img
                src="{!!  $item->getImageUrl() !!}"
                alt="{{ $item->name }}"
                style="width:70px"/></td>
    <td style="width: 70%;"><span
                style="font-size:14px; font-weight:bold;">{{ $item->name }}</span><br/>symbol: {{ $item->symbol }}
        <br/>Ilość: <span style="font-size: 1.5em; font-weight: bold;">{{ $quantity }} {{ $item->packing->calciation_unit }}</span><br/></td>
    @if(isset($showPosition) && $showPosition)
        <td style="width: 20%;">
            ILOŚĆ NA STANIE: {{ $item->stock->quantity }} <br/>
            @if(count($item->getPositions()))
                LOKACJA PRODUKTÓW: <br/>
                @foreach($item->getPositions() as $position)
                    Alejka: {{ $position->lane }} <br/>
                    Regał: {{ $position->bookstand }} </br>
                    Półka: {{ $position->shelf }} </br>
                    Pozycja: {{ $position->position }} </br>
                    Ilość: {{ $position->position_quantity }} </br>
                @endforeach
            @endif
        </td>
    @endif
</tr>
