<tr>
    <td class="wz__position"><img
                src="{!!  $item->getImageUrl() !!}"
                alt="{{ $item->name }}"
                class="wz__image"/></td>
    <td class="wz__container"><span
                class="wz__description">{{ $item->name }}</span><br/>symbol: {{ $item->symbol }}
        <br/>Ilość: <span class="quantity">{{ $quantity }} {{ $item->packing->calculation_unit }}</span><br/>
        @if($orderItem->packet)
            Pobrane z pakietu {{ $orderItem->packet->packet_name }}: <span class="quantity">{{ $orderItem->packet->packet_product_quantity }}</span>
            <br>
            Pozostało do wydania poza pakietem: <span class="quantity">{{ $quantity - $orderItem->packet->packet_product_quantity }}</span>
        @endif
    </td>
    @if(isset($showPosition) && $showPosition)
        @if(count($item->getPositions()))
            @foreach($item->getPositions() as $position)
                <td class="wz__position">
                    Alejka: {{ $position->lane }} <br/>
                    Regał: {{ $position->bookstand }} </br>
                    Półka: {{ $position->shelf }} </br>
                    Pozycja: {{ $position->position }} </br>
                    Ilość: {{ $position->position_quantity }} </br>
                    JZ:
                    @if($orderItem->product->packing->number_of_sale_units_in_the_pack != 0)
                        {{ floor($position->position_quantity / $orderItem->product->packing->number_of_sale_units_in_the_pack) }}
                    @else
                        0
                    @endif
                    <br/>
                    JH:
                    @if($orderItem->product->packing->number_of_sale_units_in_the_pack == 0 || $position->position_quantity < 0)
                        {{ $position->position_quantity }}
                    @else
                        {{ $position->position_quantity - (floor($position->position_quantity / $orderItem->product->packing->number_of_sale_units_in_the_pack) * $orderItem->product->packing->number_of_sale_units_in_the_pack) }}
                    @endif
                    <br/><br/>
                </td>
            @endforeach
        @endif
    @endif
</tr>
