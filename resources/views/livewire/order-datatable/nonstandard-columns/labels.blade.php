<div>
    @if($labelGroupName === 'info dodatkowe')
        <button onclick="uploadFile({{ $order['id'] }})">
            Dodaj
        </button>

        @foreach($order['files'] as $file)
            <a href="{{ route('orders.getFile', ['id' => $order['id'], 'file_id' => $file['hash']]) }}" target="_blank">
                @php
                    $fileName = $file['file_name'];
                    $chunks = str_split($fileName, 8);
                @endphp

                {{ $chunks[0] }}

            </a>

            <button onclick="getFilesList({{ $order['id'] }})">
                Usuń
            </button>
        @endforeach
    @endif
    @if($labelGroupName === 'fakury zakupu')
        <div style="margin-top: 30px">
            <h5>
                @php
                    $paymentConfirmations = \App\Entities\OrderPaymentConfirmation::where('order_id', $order['id'])->get();
                @endphp

                @foreach($paymentConfirmations as $paymentConfirmation)
                    <a href="{{ $paymentConfirmation->file_url }}" target="_blank">Potwierdzenie przelewu zostało wysłane</a>
                @endforeach

            </h5>
        </div>
        <h6>Załącz potwierdzenie przelewu</h6>
        <form action="{{ route('store-payment-confirmation', $order['id']) }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" style="width: 100px">

            <button class="btn btn-primary">
                Wyślij plik
            </button>
        </form>
    @endif
    @if($labelGroupName === 'transport')
        @php
            $dateTranslations = [
                'customer_shipment_date_from' => 'kl od',
                'customer_shipment_date_to' => 'kl do',
                'consultant_shipment_date_from' => 'ko od',
                'consultant_shipment_date_to' => 'ko do',
                'warehouse_shipment_date_from' => 'ma od',
                'warehouse_shipment_date_to' => 'ma do'
            ];
        @endphp
        @foreach($order['dates'] ?? [] as $k => $date)
            @if(array_key_exists($k, $dateTranslations))
                {{ $dateTranslations[$k] ?? '' }}: {{  isset($date) ? \Carbon\Carbon::parse($date)->timezone('Europe/Warsaw')->format('m-d H:i') : 'Brak' ?? '' }}
                <br>
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
                    <i class="{{ $label['icon_name'] }}" style="font-size: 30px; background-color: {{ $label['color'] }}; color: #ffffff; padding: 10px; {{  $label['id'] === 64 && \Carbon\Carbon::create($order['payment_deadline']) < \Carbon\Carbon::now() ? 'border: solid 10px red' : '' }}"></i>
                    <div class="label-popup">{{ $label['name'] }}</div>
                </span>
            @endforeach
        @endif
    </div>
</div>
