@foreach($package->products as $item)
    <table border="0" cellpadding="1" cellspacing="1" style="width: 100%;">
        <tr>
            <td style="width:100px"><img
                    src="{!!  $item->getImageUrl() !!}"
                    alt="{{ $item->name }}"
                    style="width:70px"/></td>
            <td><span
                    style="font-size:14px; font-weight:bold;">{{ $item->name }}</span><br/>symbol: {{ $item->symbol }}
                <br/>Ilość: {{ $item->pivot->quantity }} {{ $item->packing->calciation_unit }}<br/></td>
            @if(isset($showPosition) && $showPosition)
                <td>
                    ILOŚĆ NA STANIE: {{ $item->stock->quantity }} <br/>
                    @if(count($item->getPositions()))
                        LOKACJA PRODUKTÓW: <br/>
                        @foreach($item->getPositions() as $position)
                            Alejka: {{ $position->lane }} <br/>
                            Regał: {{ $position->bookstand }} </br>
                            Półka: {{ $position->shelf }} </br>
                            Pozycja: {{ $position->position }} </br>
                            Ilość: {{ $position->position_quantity }}
                        @endforeach
                    @endif
                </td>
            @endif
        </tr>
        <tr>
            <td colspan="3">
                <hr/>
            </td>
        </tr>
    </table>

@endforeach
