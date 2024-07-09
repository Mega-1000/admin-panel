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

                    Dane osoby obsługującej:
                    email: {{ Order::find($orderId)->warehouse->warehouse_email }}
                    numer telefonu: {{ Order::find($orderId)->warehouse->warehouse_email }}
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

            @php
                $hasLabel = false;
                if (!empty($order['labels'])) {
                    foreach ($order['labels'] as $label) {
                        if ($label['id'] === 270)
                        {
                            $hasLabel = true;
                            break;
                        }

                        if ($label['id'] === 275) {
                            $hasLabel = true;
                            break;
                        }
                         if ($label['id'] === 243) {
                            $hasLabel = true;
                            break;
                        }
                    }
                }
            @endphp
        @foreach($order['dates'] ?? [] as $k => $date)
            @if(array_key_exists($k, $dateTranslations))
                {{ $dateTranslations[$k] ?? '' }}: {{  isset($date) ? \Carbon\Carbon::parse($date)->timezone('Europe/Warsaw')->format('m-d H:i') : 'Brak' ?? '' }}
                <br>
            @endif
        @endforeach

        @php
            $date = false;

            try {
                $date = \Carbon\Carbon::create($order['last_confirmation'])->isToday();
            } catch (\Exception $e) {
            }
        @endphp

        @if($date)
            <div style="color: green">
                Magazyn potwierdził, że nie zamówienie nie wyjedzie jutro
            </div>
        @endif

        @if($hasLabel)
            <hr>
            Numer telefonu do działu spedycji: {{ $order['warehouse']['shipment_after_pay_phone'] ?? '' }}
            <hr>
        @endif


            @php
            $hasLabel276 = false;
            $hasLabel279 = false;
            if (!empty($order['labels'])) {
                foreach ($order['labels'] as $label) {
                    if ($label['id'] === 276 )
                    {
                        $hasLabel276 = true;
                        break;
                    }

                    if ($label['id'] === 279) {
                        $hasLabel279 = true;
                        break;
                    }
                }
            }
        @endphp

        @if ($hasLabel276 || $hasLabel279)
            <div class="mt-4">
                <hr>
                ZADZWOŃ DO KIEROWCY
                <br>
                pod numer: {{ $order['driver_phone'] ?? '' }}
                <br>
                Wpisz datę następnego kontaktu:
                <form action="{{ route('save-contact-to-driver', $order['id']) }}">
                    <input type="datetime-local" id="next_contact_date" name="next_contact_date">

                    <button class="btn btn-success" name="successed" value="true">
                        Kontakt udany
                    </button>

{{--                    @if($hasLabel279)--}}
                        <button class="btn btn-danger" name="unsuccessed" value="true">
                            Kontakt nieudany
                        </button>
{{--                    @endif--}}
                </form>
                <hr>
            </div>
        @endif
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
                        onmouseover="showLabelName(this, '{{ $label['name'] }}', '{{ $label['created_at'] }}')"
                        onmouseout="hideLabelName(this)"
                    >
                <i class="{{ $label['icon_name'] }}" style="font-size: 30px; background-color: {{ $label['color'] }}; color: #ffffff; padding: 10px;"></i>
                <div class="label-popup"></div>
            </span>
                @endforeach
            @endif
        </div>

    @if($labelGroupName === 'produkcja')
            {{ App\Entities\OrderWarehouseNotification::where('order_id', $order['id'])->orderBy('created_at', 'desc')->first()?->contact_person ?? '' }}
            {{ App\Entities\OrderWarehouseNotification::where('order_id', $order['id'])->orderBy('created_at', 'desc')->first()?->contact_person_phone ?? '' }}
            @if(!App\Entities\OrderWarehouseNotification::where('order_id', $order['id'])->orderBy('created_at', 'desc')->first()?->contact_person_phone)
                {{ App\Entities\OrderWarehouseNotification::where('order_id', $order['id'])->orderBy('created_at', 'desc')->first()?->warehouse->property->phone ?? '' }}
            @endif
            {{ App\Entities\OrderWarehouseNotification::where('order_id', $order['id'])->orderBy('created_at', 'desc')->first()?->created_at ?? '' }}
            @if(App\Entities\OrderWarehouseNotification::where('order_id', $order['id'])->orderBy('created_at', 'desc')->first()?->warehouse->warehouse_email)
                {{ strstr(App\Entities\OrderWarehouseNotification::where('order_id', $order['id'])->orderBy('created_at', 'desc')->first()?->warehouse->warehouse_email ?? '', '@', true) }}@
            @endif
    @endif
</div>
