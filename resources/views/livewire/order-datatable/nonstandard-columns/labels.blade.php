<div>
    @php
        $order = App\Entities\Order::find($order['id']);
    @endphp
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

        @php
            $hasLabel224 = false;
            if (!empty($order['labels'])) {
                foreach ($order['labels'] as $label) {
                    if ($label['id'] === 224 )
                    {
                        $hasLabel224 = true;
                        break;
                    }
                }
            }

            // label 265
            $hasLabel265 = false;
            if (!empty($order['labels'])) {
                foreach ($order['labels'] as $label) {
                    if ($label['id'] === 265 )
                    {
                        $hasLabel265 = true;
                        break;
                    }
                }
            }
        @endphp
        @if ($hasLabel224)
            <hr>
                <a style="color: green"  href="/auctions/{{ $order['chat']['auctions'][0]['id'] }}/end" target="_blank">
                    Przetarg na styropian aktywny! Wysłano {{ \App\Entities\ChatAuction::find($order['chat']['auctions'][0]['id'])->firms->count() }} Zapytań - otrzymano {{ \App\Entities\ChatAuction::find($order['chat']['auctions'][0]['id'])->offers->unique('firm')->count() }} ofert

                    <br>

                    Koniec przetargu: {{ \App\Entities\ChatAuction::find($order['chat']['auctions'][0]['id'])->end_of_auction }}
                </a>
            <hr>
        @endif

        @if ($hasLabel265)
            <hr>
                <a style="color: red"  href="/auctions/{{ $order['chat']['auctions'][0]['id'] }}/end" target="_blank">
                    Zadzwoń do klienta i poinformuj go o zakończonym przetargu na styropian! Wysłano {{ \App\Entities\ChatAuction::find($order['chat']['auctions'][0]['id'])->firms->count() }} Zapytań - otrzymano {{ \App\Entities\ChatAuction::find($order['chat']['auctions'][0]['id'])->offers->unique('firm')->count() }} ofert

                    <br>

                     Przetarg został zakończony: {{ \App\Entities\ChatAuction::find($order['chat']['auctions'][0]['id'])->end_of_auction }}
                </a>
            <br>
            <br>

            <form action="/admin/add-additional-info/{{ $order['id'] }}" method="POST">
                @csrf
                Dodatkowe informacje
                <input type="text" name="notices" class="form-control">
                <br>
                Następny kontakt
                <input type="datetime-local" name="next_contact_date" class="form-control">
                <input type="submit" value="Zapisz" class="btn btn-primary">
            </form>
            <hr>
        @endif


        @php
            $hasLabel206 = false;
            if (!empty($order['labels'])) {

                foreach ($order['labels'] as $label) {
                    if ($label['id'] === 206)
                    {
                        $hasLabel206 = true;
                        break;
                    }
                }
            }
        @endphp

        @if ($hasLabel206)
            <div class="mt-4">
                <hr>
                Zamówienie zostało zatwierdzone! Data zatwierdzenia: {{ $order['approved_at'] }}
                <hr>
            </div>
        @endif

        @php
            $hasLabel95 = false;
            if (!empty($order['labels'])) {
                foreach ($order['labels'] as $label) {
                    if ($label['id'] === 95)
                    {
                        $hasLabel95 = true;
                        break;
                    }
                }
            }
        @endphp

        @if($hasLabel95)
            @if($order->getValue() < 3000)
                <div style="color: red">
                    To zamówienie zawiera małą ilość styropianu. Nie możliwa będzie dostawa do klienta bezpłatnie. Należy obsłużyć klienta ręcznie.
                </div>
            @endif
        @endif
    @endif
    @if($labelGroupName === 'fakury zakupu')
        <div style="margin-top: 30px">
            <h5>
                @php
                    $paymentConfirmations = \App\Entities\OrderPaymentConfirmation::where('order_id', $order['id'])->get();
                @endphp

                @foreach($paymentConfirmations as $paymentConfirmation)
                    <hr>
                    <a href="{{ $paymentConfirmation->file_url }}" target="_blank">Potwierdzenie przelewu zostało wysłane</a>

                    <br>
                    <br>
                    Dane osoby obsługującej:
                    <br>
                    email: {{ Order::find($order['id'])->warehouse->warehouse_email }}
                    <br>
                    numer telefonu: {{ Order::find($order['id'])->warehouse->property->phone }}
                    <hr>
                @endforeach
            </h5>
        </div>
        @if($order['invoice_buying_warehouse_file'])
                <a href="{{ $order['invoice_buying_warehouse_file'] }}" target="_blank" style="color: green"> XML Faktury zakupu gotowy! </a>
        @else
            <a href="/styro-chatrs/{{ $order['id'] }}">
                Generuj fakturę zakupu
            </a>
        @endif

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
                Magazyn potwierdził, że zamówienie nie wyjedzie jutro
            </div>
        @endif

        @if($hasLabel)
            <hr>
            Numer telefonu do działu spedycji: {{ $order['warehouse']['shipment_after_pay_phone'] ?? '' }}
            Email do działu spedycji: {{ $order['warehouse']['shipment_after_pay_email'] ?? '' }}
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

        @php
            $hasLabel77 = false;
            if (!empty($order['labels'])) {
                foreach ($order['labels'] as $label) {
                    if ($label['id'] === 77 )
                    {
                        $hasLabel77 = true;
                        break;
                    }
                }
            }
        @endphp

    @if($labelGroupName === 'produkcja')
        @php
            $warehouse = $order->warehouse;
            if ($order->orderWarehouseNotification?->employee_id && $order->orderWarehouseNotification->employee->is_performing_avization) {
                $warehouseMail = $order->orderWarehouseNotification->employee->email;
            }

            if ($warehouse && $warehouse->firm) {
                $warehouseMail = $warehouse->firm->email;
            }
        @endphp
            {{ App\Entities\OrderWarehouseNotification::where('order_id', $order['id'])->orderBy('created_at', 'desc')->first()?->contact_person ?? '' }}
            {{ App\Entities\OrderWarehouseNotification::where('order_id', $order['id'])->orderBy('created_at', 'desc')->first()?->contact_person_phone ?? '' }}
            @if(!App\Entities\OrderWarehouseNotification::where('order_id', $order['id'])->orderBy('created_at', 'desc')->first()?->contact_person_phone)
                {{ App\Entities\OrderWarehouseNotification::where('order_id', $order['id'])->orderBy('created_at', 'desc')->first()?->warehouse->property->phone ?? '' }}
            @endif
            {{ App\Entities\OrderWarehouseNotification::where('order_id', $order['id'])->orderBy('created_at', 'desc')->first()?->created_at ?? '' }}
            @if(App\Entities\OrderWarehouseNotification::where('order_id', $order['id'])->orderBy('created_at', 'desc')->first()?->warehouse->warehouse_email)
                {{ strstr($warehouseMail ?? '', '@', true) }}@
                @php($amountOfMonits =  App\MailReport::where('subject', 'like', '%Ponownie prosimy o potwierdzenie awizacji do%')->where('body', 'like', '%' . $order['id'] . '%')->count())

                @if($amountOfMonits > 0 && $hasLabel77)
                    <div style="color: red; margin-top: 20px">
                        Wysłano {{ $amountOfMonits }} ponagleń w sprawie awizacji
                    </div>
                @endif
            @endif


    @endif
</div>
