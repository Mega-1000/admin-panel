@php
    // Eager load all necessary relationships
    $or = App\Entities\Order::with([
        'labels',
        'files',
        'warehouse.property',
        'orderWarehouseNotification.employee',
        'warehouse.firm',
        'chat.auctions.firms',
        'chat.auctions.offers',
        'paymentConfirmations'
    ])->findOrFail($order['id']);

    // Pre-compute label checks
    $labelIds = $or->labels->pluck('id')->toArray();
    $labelChecks = [
        224 => in_array(224, $labelIds),
        265 => in_array(265, $labelIds),
        206 => in_array(206, $labelIds),
        95 => in_array(95, $labelIds),
        270 => in_array(270, $labelIds) || in_array(275, $labelIds) || in_array(243, $labelIds),
        276 => in_array(276, $labelIds),
        279 => in_array(279, $labelIds),
        77 => in_array(77, $labelIds)
    ];

    $chatAuction = $or->chat->auctions->first();
    $warehouseNotification = $or->orderWarehouseNotification;
@endphp

<div>
    @if($labelGroupName === 'info dodatkowe')
        <button onclick="uploadFile({{ $or->id }})">
            Dodaj
        </button>

        @foreach($or->files as $file)
            <a href="{{ route('orders.getFile', ['id' => $or->id, 'file_id' => $file['hash']]) }}" target="_blank">
                @php
                    $fileName = $file['file_name'];
                    $chunks = str_split($fileName, 8);
                @endphp
                {{ $chunks[0] }}
            </a>
            <button onclick="getFilesList({{ $or->id }})">
                Usuń
            </button>
        @endforeach

        @if ($labelChecks[224] && $chatAuction)
            <hr>
            <a style="color: green" href="/auctions/{{ $chatAuction->id }}/end" target="_blank">
                Przetarg na styropian aktywny! Wysłano {{ $chatAuction->firms->count() }} Zapytań - otrzymano {{ $chatAuction->offers->unique('firm')->count() }} ofert
                <br>
                Koniec przetargu: {{ $chatAuction->end_of_auction }}
            </a>
            <hr>
        @endif

        @if ($labelChecks[265] && $chatAuction)
            <hr>
            <a style="color: red" href="/auctions/{{ $chatAuction->id }}/end" target="_blank">
                Zadzwoń do klienta i poinformuj go o zakończonym przetargu na styropian! Wysłano {{ $chatAuction->firms->count() }} Zapytań - otrzymano {{ $chatAuction->offers->unique('firm')->count() }} ofert
                <br>
                Przetarg został zakończony: {{ $chatAuction->end_of_auction }}
            </a>
            <br>
            <br>
            <form action="/admin/add-additional-info/{{ $or->id }}" method="POST">
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

        @if ($labelChecks[206])
            <div class="mt-4">
                <hr>
                Zamówienie zostało zatwierdzone! Data zatwierdzenia: {{ $or->approved_at }}
                <hr>
            </div>
        @endif

        @if($labelChecks[95] && $or->getValue() < 3000)
            <div style="color: red">
                Zamówienie zawiera mało styropianu. Dostawa nie będzie bezpłatna. Obsłuż klienta ręcznie.
                <br>
                <div>
                    @php
                        $variation = app(\App\Http\Controllers\OrdersController::class)->getNearestVariation($or);
                    @endphp
                    Najbliższa wariacja to: {{ $variation['product_name_supplier'] }} w odległości {{ round($variation['distance'], 2) }} km {{ \App\Entities\Warehouse::find($variation['warehouse_id'])->address->city }}
                </div>
            </div>
        @endif
    @endif

    @if($labelGroupName === 'fakury zakupu')
        <div style="margin-top: 30px">
            <h5>
                @foreach($or->paymentConfirmations as $paymentConfirmation)
                    <hr>
                    <a href="{{ $paymentConfirmation->file_url }}" target="_blank">Potwierdzenie przelewu zostało wysłane</a>
                    <br>
                    <br>
                    Dane osoby obsługującej:
                    <br>
                    email: {{ $or->warehouse->warehouse_email }}
                    <br>
                    numer telefonu: {{ $or->warehouse->property->phone }}
                    <hr>
                @endforeach
            </h5>
        </div>
        @if($or->invoice_buying_warehouse_file)
            <a href="{{ $or->invoice_buying_warehouse_file }}" target="_blank" style="color: green"> XML Faktury zakupu gotowy! </a>
        @else
            <a href="/styro-chatrs/{{ $or->id }}">
                Generuj fakturę zakupu
            </a>
        @endif

        <h6>Załącz potwierdzenie przelewu</h6>
        <form action="{{ route('store-payment-confirmation', $or->id) }}" method="post" enctype="multipart/form-data">
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

        @foreach($or->dates ?? [] as $k => $date)
            @if(array_key_exists($k, $dateTranslations))
                {{ $dateTranslations[$k] ?? '' }}: {{ isset($date) ? \Carbon\Carbon::parse($date)->timezone('Europe/Warsaw')->format('m-d H:i') : 'Brak' ?? '' }}
                <br>
            @endif
        @endforeach

        @php
            $date = false;
            try {
                $date = \Carbon\Carbon::create($or->last_confirmation)->isToday();
            } catch (\Exception $e) {
            }
        @endphp

        @if($date)
            <div style="color: green">
                Magazyn potwierdził, że zamówienie nie wyjedzie jutro
            </div>
        @endif

        @if($labelChecks[270])
            <hr>
            Numer telefonu do działu spedycji: {{ $or->warehouse->shipment_after_pay_phone ?? '' }}
            Email do działu spedycji: {{ $or->warehouse->shipment_after_pay_email ?? '' }}
            <hr>
        @endif

        @if ($labelChecks[276] || $labelChecks[279])
            <div class="mt-4">
                <hr>
                ZADZWOŃ DO KIEROWCY
                <br>
                pod numer: {{ $or->driver_phone ?? '' }}
                <br>
                Wpisz datę następnego kontaktu:
                <form action="{{ route('save-contact-to-driver', $or->id) }}">
                    <input type="datetime-local" id="next_contact_date" name="next_contact_date">
                    <button class="btn btn-success" name="successed" value="true">
                        Kontakt udany
                    </button>
                    <button class="btn btn-danger" name="unsuccessed" value="true">
                        Kontakt nieudany
                    </button>
                </form>
                <hr>
            </div>
        @endif
    @endif

    <div class="label-container">
        @foreach($or->labels->filter(function ($label) use ($labelGroupName) {
            return $label->label_group && $label->label_group->name === $labelGroupName;
        }) as $label)
            <span
                onclick="removeLabel({{ $or->id}}, {{ $label->id }}, {{ $label->manual_label_selection_to_add_after_removal }}, 'null', {{$label->timed}})"
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

    @if($labelGroupName === 'produkcja')
        @php
            $warehouse = $or->warehouse;
            $warehouseMail = $warehouseNotification && $warehouseNotification->employee && $warehouseNotification->employee->is_performing_avization
                ? $warehouseNotification->employee->email
                : ($warehouse && $warehouse->firm ? $warehouse->firm->email : null);
            $amountOfMonits = $labelChecks[77] ? \App\MailReport::where('subject', 'like', '%Ponownie prosimy o potwierdzenie awizacji do%')
                ->where('body', 'like', '%' . $or->id . '%')
                ->count() : 0;
        @endphp
        {{ $warehouseNotification->contact_person ?? '' }}
        {{ $warehouseNotification->contact_person_phone ?? $warehouse->property->phone ?? '' }}
        {{ $warehouseNotification->created_at ?? '' }}
        @if($warehouse && $warehouse->warehouse_email)
            {{ strstr($warehouseMail ?? '', '@', true) }}@
            @if($amountOfMonits > 0 && $labelChecks[77])
                <div style="color: red; margin-top: 20px">
                    Wysłano {{ $amountOfMonits }} ponagleń w sprawie awizacji
                </div>
            @endif
        @endif
    @endif
</div>
