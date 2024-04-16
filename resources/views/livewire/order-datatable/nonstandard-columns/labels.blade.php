<div>
    @if($labelGroupName === 'info dodatkowe')
        <button onclick="uploadFile({{ $order['id'] }})">
            Dodaj
        </button>

        @foreach($order['files'] as $file)
            <a href="{{ route('orders.getFile', ['id' => $order['id'], 'file_id' => $file['hash']]) }}" target="_blank">
                {{ $file['file_name'] }}
            </a>

            <button onclick="getFilesList({{ $order['id'] }})">
                Usuń
            </button>
        @endforeach
    @endif

        @if($labelGroupName === 'transport')
            @php
                $dateTranslations = [
                    'customer_shipment_date_from' => 'data klient od',
                    'customer_shipment_date_to' => 'data klient do',
                    'consultant_shipment_date_from' => 'data konsultanta od',
                    'consultant_shipment_date_to' => 'data konsultanta do',
                    'warehouse_shipment_date_from' => 'data magazynu od',
                    'warehouse_shipment_date_to' => 'data magazynu do'
                ];

                function formatDateTime($dateTime) {
                    return \Carbon\Carbon::parse($dateTime)->timezone('Europe/Warsaw')->format('m-d H:i');
                }
            @endphp

            <strong>data klient</strong><br>
            @foreach(['customer_shipment_date_from', 'customer_shipment_date_to'] as $key)
                @if(isset($order['dates'][$key]))
                    {{ $dateTranslations[$key] }} {{ formatDateTime($order['dates'][$key]) }}<br>
                @endif
            @endforeach
        @endif


    <div class="label-container">
        @if(!empty($order))
            @foreach(
                !empty($order['labels']) &&
                count($order['labels']) > 0
                    ? array_filter($order['labels'], function ($label) use($labelGroupName) { return !empty($label['label_group']) ? $label['label_group']['name'] === $labelGroupName : null; })
                    : [] as $label
            )
                <span
                    onclick="
                    removeLabel({{ $order['id']}}, {{ $label['id'] }}, {{ $label['manual_label_selection_to_add_after_removal'] }}, 'null', {{$label['timed']}})"
                    class="label-wrapper"
                    style="cursor: pointer"
                    onmouseover="showLabelName(this, '{{ $label['name'] }}')"
                    onmouseout="hideLabelName(this)"
                >
                    <i class="{{ $label['icon_name'] }}" style="font-size: 30px; background-color: {{ $label['color'] }}; color: #ffffff; padding: 10px"></i>
                    <div class="label-popup">{{ $label['name'] }}</div>
                </span>
            @endforeach
        @endif
    </div>
</div>
