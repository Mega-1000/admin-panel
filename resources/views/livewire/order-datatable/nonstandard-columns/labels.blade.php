
@php
    $or = App\Entities\Order::with(['labels.labelGroup', 'files', 'chat.auctions', 'warehouse.property'])->find($order['id']);
    $labels = collect($or->labels);
    $hasLabel224 = $labels->contains('id', 224);
    $hasLabel265 = $labels->contains('id', 265);
    $hasLabel206 = $labels->contains('id', 206);
    $hasLabel95 = $labels->contains('id', 95);
    $hasLabel276 = $labels->contains('id', 276);
    $hasLabel279 = $labels->contains('id', 279);
    $hasLabel77 = $labels->contains('id', 77);
    $hasLabel270 = $labels->contains('id', 270);
    $hasLabel275 = $labels->contains('id', 275);
    $hasLabel243 = $labels->contains('id', 243);
    $hasLabel281 = $labels->contains('id', 281);
@endphp


@if($labelGroupName === 'info dodatkowe')
    <button onclick="uploadFile({{ $or->id }})">Dodaj</button>

    @foreach($or->files as $file)
        <a href="{{ route('orders.getFile', ['id' => $or->id, 'file_id' => $file['hash']]) }}" target="_blank">
            {{ substr($file['file_name'], 0, 8) }}
        </a>
        <button onclick="getFilesList({{ $or->id }})">Usuń</button>
    @endforeach

    @if ($hasLabel224 && $or->chat && $or->chat->auctions->isNotEmpty())
        <hr>
        @php
            $auction = $or->chat->auctions->first();
        @endphp
        <a style="color: green" href="/auctions/{{ $auction->id }}/end" target="_blank">
            Przetarg na styropian aktywny! Wysłano {{ $auction->firms->count() }} Zapytań - otrzymano {{ $auction->offers->unique('firm')->count() }} ofert
            <br>
            Koniec przetargu: {{ $auction->end_of_auction }}
        </a>
        <hr>
    @endif

    @if ($hasLabel265 && $or->chat && $or->chat->auctions->isNotEmpty())
        <hr>
        @php
            $auction = $or->chat->auctions->first();
        @endphp
        <a style="color: red" href="/auctions/{{ $auction->id }}/end" target="_blank">
            Zadzwoń do klienta i poinformuj go o zakończonym przetargu na styropian! Wysłano {{ $auction->firms->count() }} Zapytań - otrzymano {{ $auction->offers->unique('firm')->count() }} ofert
            <br>
            Przetarg został zakończony: {{ $auction->end_of_auction }}
        </a>
        <br><br>
        <form action="/admin/add-additional-info/{{ $or->id }}" method="POST">
            @csrf
            Dodatkowe informacje
            <input type="text" name="notices" class="form-control">
            <br>
            Następny kontakt
            <input type="datetime-local" name="next_contact_date" class="form-control">
            <br>
            <input type="checkbox" name="sendEmail">
            Wyślij informacje na e-mail
            <br>
            <input type="submit" name="normal" value="Klient porzebuje jeszcze czasu" class="btn btn-primary">
            <input type="submit" name="notAnswered" value="Nie odebrano telefonu" class="btn btn-danger">
            <input type="submit" name="clientClosed" value="Klient zdecydowany" class="btn btn-success">
        </form>
        <hr>
    @endif

    @if ($hasLabel206)
        <div class="mt-4">
            <hr>
            Zamówienie zostało zatwierdzone! Data zatwierdzenia: {{ $or->approved_at }}
            <hr>
        </div>
    @endif

    @if($hasLabel95 && $or->getValue() < 3000 && Order::where('id', $or->id)->whereHas('items', function ($query) {    $query->whereHas('product', function ($subQuery) {        $subQuery->where('variation_group', 'styropiany');});}))
        <div style="color: red">
            Zamówienie zawiera mało styropianu. Dostawa nie będzie bezpłatna. Obsłuż klienta ręcznie.
            <br>
            <div>
                @php
                    $variation = app(\App\Http\Controllers\OrdersController::class)->getNearestVariation($or);
                @endphp
                @if($variation)
                    Najbliższa wariacja to: {{ $variation['product_name_supplier'] }} w odległości {{ round($variation['distance'], 2) }} km {{ \App\Entities\Warehouse::find($variation['warehouse_id'])->address->city }}
                @endif
            </div>
        </div>
    @endif
@endif

@if($labelGroupName === 'fakury zakupu')
    <div style="margin-top: 30px">
        <h5>
            @php
                $paymentConfirmations = \App\Entities\OrderPaymentConfirmation::where('order_id', $or->id)->get();
            @endphp

            @foreach($paymentConfirmations as $paymentConfirmation)
                <hr>
                <a href="{{ $paymentConfirmation->file_url }}" target="_blank">Potwierdzenie przelewu zostało wysłane</a>
                <br><br>
                Dane osoby obsługującej:
                <br>
                email: {{ $or->warehouse->warehouse_email ?? '' }}
                <br>
                numer telefonu: {{ $or->warehouse->property->phone ?? '' }}
                <hr>
            @endforeach
        </h5>
    </div>
    @if($or->invoice_buying_warehouse_file)
        <a href="{{ $or->invoice_buying_warehouse_file }}" target="_blank" style="color: green"> XML Faktury zakupu gotowy! </a>
    @endif
    <br>
    <a href="/styro-chatrs/{{ $or->id }}">Generuj fakturę zakupu</a>

    <h6>Załącz potwierdzenie przelewu</h6>
    <form action="{{ route('store-payment-confirmation', $or->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" style="width: 100px">
        <button class="btn btn-primary">Wyślij plik</button>
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

    @foreach($or->dates()?->first()?->toArray() ?? [] as $k => $date)
        @if(array_key_exists($k, $dateTranslations))
            {{ $dateTranslations[$k] ?? '' }}: {{ isset($date) ? \Carbon\Carbon::parse($date)->timezone('Europe/Warsaw')->format('m-d H:i') : 'Brak' }}
            <br>
        @endif
    @endforeach

    @if($or->last_confirmation && \Carbon\Carbon::parse($or->last_confirmation)->isToday())
        <div style="color: green">Magazyn potwierdził, że zamówienie nie wyjedzie jutro</div>
    @endif

    @if($hasLabel270 || $hasLabel275 || $hasLabel243)
        <hr>
        @if(!$or->warehouse->shipment_after_pay_email)
            <div style="color: red">
                !! Uwaga email do logistyki po wpłacie pieniędzy nie jest dostępny !! <br>
                Wpisz email do logistyki po wpłacie pieniędzy magazunu <a href="https://admin.mega1000.pl/admin/warehouses/{{  $or->warehouse->id }}/edit">{{ $or->warehouse->symbol }}</a>
            </div>
        @endif
        Numer telefonu do działu spedycji: {{ $or->warehouse->shipment_after_pay_phone ?? '' }}
        Email do działu spedycji: {{ $or->warehouse->shipment_after_pay_email ?? '' }}
        <hr>
    @endif

    @if($hasLabel281)
        <hr>
        ZADZWOŃ DO KIEROWCY
        <br>
        pod numer: {{ $or->driver_phone ?? '' }}
        <br>
    @endif

    @if ($hasLabel276 || $hasLabel279)
        <div class="mt-4">
            <hr>
            ZADZWOŃ DO KIEROWCY
            <br>
            pod numer: {{ $or->driver_phone ?? '' }}
            <br>
            Wpisz datę następnego kontaktu:
            <form action="{{ route('save-contact-to-driver', $or->id) }}">
                <input type="datetime-local" id="next_contact_date" name="next_contact_date">
                <button class="btn btn-success" name="successed" value="true">Kontakt udany</button>
                <button class="btn btn-danger" name="unsuccessed" value="true">Kontakt nieudany</button>
            </form>
            <hr>
        </div>
    @endif
@endif

<div class="label-container">
    @foreach($labels->filter(function ($label) use($labelGroupName) {
        return $label->labelGroup && $label->labelGroup->name === $labelGroupName;
    }) as $label)
        <span
            onclick="removeLabel({{ $or->id}}, {{ $label->id }}, {{ $label->manual_label_selection_to_add_after_removal ?? 'null' }}, 'null', {{$label->timed ? 'true' : 'false'}})"
            class="label-wrapper"
            style="cursor: pointer"
            onmouseover="showLabelName(this, '{{ $label->name }}', '{{ $label->created_at }}')"
            onmouseout="hideLabelName(this)"
        >
            <i class="{{ $label->icon_name }}" style="font-size: 30px; background-color: {{ $label->color }}; color: #ffffff; padding: 10px;"></i>
            <div class="label-popup"></div>
        </span>
    @endforeach
</div>

@if($labelGroupName === 'produkcja' && $notification = \App\Entities\OrderWarehouseNotification::where('order_id', $or->id)->latest()->first())
    <div style="text-align: center">
        Awizacje obsługuje {{ $notification && $notification->employee_id && $notification->employee->is_performing_avization ? 'Pracownik' : 'Magazyn' }}
        <br>
        &#8595;
        <br>
        Awizacja została wysłana:
        @php
            $warehouse = $or->warehouse;
            $warehouseMail = $notification && $notification->employee_id && $notification->employee->is_performing_avization
                ? $notification->employee->email
                : ($warehouse && $warehouse->firm ? $warehouse->warehouse_email : null);

            $warehousePhone = $notification && $notification->employee_id && $notification->employee->is_performing_avization
                ? $notification->employee->phone
                : ($warehouse && $warehouse->property ? $warehouse->property->phone : null);
        @endphp

        {{ $warehousePhone }}
        {{ $notification?->created_at ?? '' }}
        @if($warehouseMail)
            {{ strstr($warehouseMail ?? '', '@', true) }}@
            @php($amountOfMonits = App\MailReport::where('subject', 'like', '%Ponownie prosimy o potwierdzenie awizacji do%')->where('body', 'like', '%' . $or->id . '%')->count())
            @if($amountOfMonits > 0 && $hasLabel77)
                <div style="color: red; margin-top: 20px">
                    Wysłano {{ $amountOfMonits }} ponagleń w sprawie awizacji
                </div>
            @endif
        @endif
        <br>
        @php($notification = \App\Entities\OrderWarehouseNotification::where('order_id', $or->id)->where('contact_person', '!=', null)->first())
        @if ($notification)
            &#8595;
            <br>
            Podano osobę kontaktową: {{ $notification->contact_person ?? '' }}
            telefon: {{ $notification?->contact_person_phone ?? '' }}
        @endif
    </div>
@endif
