@php
    $data = $order['packages'];
    $html = $order['packages_values'];
    $cancelled = 0;
@endphp
@if(count($data) !== 0)
    @if($order['packages'] && collect($order['packages'])->firstWhere('type', 'not_calculable'))
        @php
            $html = '<div style="border: solid blue 4px" >';
        @endphp
    @else
        @php
            $html = '<div style="border: solid green 4px" >';
        @endphp
    @endif
@endif

@foreach($data as $key => $value)
    @php
        $color = '';
        switch($value->status) {
            case 'DELIVERED':
                $color = '#87D11B';
                break;
            case 'SENDING':
                $color = '#4DCFFF';
                break;
            case 'WAITING_FOR_SENDING':
                $color = '#5537f0';
                break;
        }
        if ($value->status === 'CANCELLED') {
            $cancelled++;
        }
    @endphp

    @if($value->status !== 'SENDING' && $value->status !== 'DELIVERED' && $value->status !== 'CANCELLED')
        <div style="display: flex; align-items: center; flex-direction: column;">
            <div style="display: flex; align-items: stretch;">
                <p style="margin: 8px 0 0 0;">{{ $row->orderId . '/' . $value->number }}</p>
                @php
                    $name = $value->container_type;
                    if ($value->symbol) {
                        $name = $value->symbol;
                    }
                @endphp
                <p style="margin: 8px 8px 0 8px;">{{ $name }}</p>
            </div>

            @if($value->status === 'WAITING_FOR_CANCELLED')
                <p>WYSŁANO DO ANULACJI</p>
            @endif

            @if($value->status === 'REJECT_CANCELLED')
                <p style="color:red;">ANULACJA ODRZUCONA</p>
            @endif

            @if($value->letter_number === null)
                @if($value->status !== 'CANCELLED' && $value->status !== 'WAITING_FOR_CANCELLED' && $value->delivery_courier_name !== 'GIELDA' && $value->service_courier_name !== 'GIELDA' && $value->delivery_courier_name !== 'ODBIOR_OSOBISTY' && $value->service_courier_name !== 'ODBIOR_OSOBISTY')
                    <div style="display: flex;">
                        <button class="btn btn-success" id="package-{{ $value->id }}" onclick="sendPackage({{ $value->id }}, {{ $value->order_id }})">Wyślij</button>
                        <button class="btn btn-danger" onclick="deletePackage({{ $value->id }}, {{ $value->order_id }})">Usuń</button>
                        <button class="btn btn-info" onclick="createSimilar({{ $value->id }}, {{ $value->order_id }})">Podobna</button>
                    </div>
                @endif
            @endif

            @if($value->service_courier_name === 'INPOST' || $value->service_courier_name === 'ALLEGRO-INPOST')
                <a target="_blank" href="{{ '/admin/orders/packages/' . $value->id . '/sticker' }}"><p>{{ $value->letter_number }}</p></a>
                <div>
                    @if($value->cash_on_delivery !== null && $value->cash_on_delivery > 0)
                        <span>{{ $value->cash_on_delivery }} zł</span>
                    @endif
                    <a target="_blank" style="color: green; font-weight: bold; color: #FFFFFF; display: inline-block; margin-top: 5px; margin-left: 5px; padding: 5px; background-color:{{ $color }}" href="{{ 'https://inpost.pl/sledzenie-przesylek?number=' . $value->letter_number }}"><i class="fas fa-shipping-fast"></i></a>
                </div>
            @elseif($value->delivery_courier_name === 'DPD')
                <a target="_blank" href="{{ '/storage/dpd/protocols/protocol' . $value->letter_number . '.pdf' }}"><p>{{ $value->sending_number }}</p></a>
                <a target="_blank" href="{{ '/admin/orders/packages/' . $value->id . '/sticker' }}"><p>{{ $value->letter_number }}</p></a>
                <div>
                    @if($value->cash_on_delivery !== null && $value->cash_on_delivery > 0)
                        <span>{{ $value->cash_on_delivery }} zł</span>
                    @endif
                    <a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; margin-top: 5px;padding: 5px;margin-left: 5px; background-color:{{ $color }}" href="{{ 'https://tracktrace.dpd.com.pl/parcelDetails?typ=1&p1=' . $value->letter_number }}"><i class="fas fa-shipping-fast"></i></a>
                </div>
            @elseif($value->delivery_courier_name === 'POCZTEX')
                <a target="_blank" href="{{ '/admin/orders/packages/' . $value->id . '/sticker' }}"><p>{{ $value->letter_number }}</p></a>
                <div>
                    @if($value->cash_on_delivery !== null && $value->cash_on_delivery > 0)
                        <span>{{ $value->cash_on_delivery }} zł</span>
                    @endif
                    <a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; margin-top: 5px;padding: 5px;margin-left: 5px; background-color:{{ $color }}" href="{{ 'http://www.pocztex.pl/sledzenie-przesylek/?numer=' . $value->letter_number }}"><i class="fas fa-shipping-fast"></i></a>
                </div>
            @elseif($value->delivery_courier_name === 'JAS')
                <a target="_blank" href="{{ '/admin/orders/packages/' . $value->id . '/sticker' }}"><p>{{ $value->letter_number }}</p></a>
                <a target="_blank" href="{{ '/storage/jas/labels/label' . $value->sending_number . '.pdf' }}"><p>{{ $value->letter_number }}</p></a>
            @elseif($value->delivery_courier_name === 'GIELDA')
                <a target="_blank" href="{{ '/admin/orders/packages/' . $value->id . '/sticker' }}"><p>{{ $value->letter_number }}</p></a>
            @elseif($value->delivery_courier_name === 'ODBIOR_OSOBISTY')
                <a target="_blank" href="{{ '/admin/orders/packages/' . $value->id . '/sticker' }}"><p>{{ $value->letter_number }}</p></a>
            @elseif($value->delivery_courier_name === 'GLS')
                <a target="_blank" href="{{ '/admin/orders/packages/' . $value->id . '/sticker' }}"><p>{{ $value->letter_number }}</p>
                    <div>
                        @if($value->cash_on_delivery !== null && $value->cash_on_delivery > 0)
                            <span>{{ $value->cash_on_delivery }} zł</span>
                        @endif
                        <a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; padding: 5px; margin-top: 5px;margin-left: 5px; background-color:{{ $color }}" href="{{ 'https://gls-group.eu/PL/pl/sledzenie-paczek?match=' . $value->letter_number }}"><i class="fas fa-shipping-fast"></i></a>
                    </div>
                    </p></a>
            @elseif($value->delivery_courier_name === 'DB')
                <a target="_blank" href="{{ '/storage/db_schenker/protocols/protocol' . $value->sending_number . '.pdf' }}"><p>LP: {{ $value->sending_number }}</p></a>
                <a target="_blank" href="{{ '/admin/orders/packages/' . $value->id . '/sticker' }}"><p>{{ $value->letter_number }}</p></a>
            @endif

            <div style="display: flex;">
                <button class="btn btn-danger" onclick="cancelPackage({{ $value->id }}, {{ $value->order_id }})">Anuluj</button>
                <button class="btn btn-info" onclick="createSimilar({{ $value->id }}, {{ $value->order_id }})">Podobna</button>
            </div>
        </div>
    @endif
@endforeach

@if($cancelled > 0)
    @php
        $url = route('orders.editPackages', ['id' => $order->orderId]);
        $html .= '<a target="_blank" href="' . $url . '">Anulowano: ' . $cancelled . '</a>';
    @endphp
@endif

{{ $html }}
