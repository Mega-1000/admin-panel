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

    <td>

        @if(isset($showPosition) && $showPosition)
            @if(count($item->getPositions()))
                @foreach($item->getPositions() as $position)
                        <div>
                            {{ $position->lane }} {{ $position->bookstand }} {{ $position->shelf }} {{ $position->position }} - {{ $position->position_quantity }}
                            <div>
                                {!! \App\Services\ProductPositioningService::renderPositioningViewHtml($position) !!}
                            </div>
                        </div>
                @endforeach
            @endif
        @endif
    </td>
</tr>
