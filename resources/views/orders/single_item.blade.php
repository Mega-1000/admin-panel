<tr>
    <td class="wz__position"><img
                src="{!!  $item->getImageUrl() !!}"
                alt="{{ $item->name }}"
                class="wz__image"/></td>
    <td class="wz__container"><span
                class="wz__description">{{ $item->name }}</span><br/>symbol: {{ $item->symbol }}
        <br/>Ilość: <span class="wz__font">{{ $quantity }} {{ $item->packing->calciation_unit }}</span><br/></td>
    @if(isset($showPosition) && $showPosition)
        @if(count($item->getPositions()))
            @foreach($item->getPositions() as $position)
                <td class="wz__position">
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
