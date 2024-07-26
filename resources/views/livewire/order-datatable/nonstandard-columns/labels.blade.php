
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

    @if($hasLabel95 && $or->getValue() < 3000)
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
    @else
        <a href="/styro-chatrs/{{ $or->id }}">Generuj fakturę zakupu</a>
    @endif

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

@if($labelGroupName === 'produkcja')
    <style>
        .timeline-container {
            max-width: 36rem;
            margin: 0 auto;
            padding: 1rem;
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
        }
        .timeline-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .timeline-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .timeline-icon {
            flex-shrink: 0;
            width: 2rem;
            height: 2rem;
            background-color: #f3f4f6;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
        }
        .timeline-content {
            flex-grow: 1;
        }
        .timeline-item-title {
            font-weight: 600;
            font-size: 1.125rem;
            margin-bottom: 0.25rem;
        }
        .timeline-item-text {
            color: #4b5563;
        }
    </style>

    <div class="timeline-container">
        <h2 class="timeline-title">Order Notification Timeline</h2>

        @php
            $notification = \App\Entities\OrderWarehouseNotification::where('order_id', $or->id)->latest()->first();
            $warehouse = $or->warehouse;
            $warehouseMail = $notification && $notification->employee_id && $notification->employee->is_performing_avization
                ? $notification->employee->email
                : ($warehouse && $warehouse->firm ? $warehouse->warehouse_email : null);

            $warehousePhone = $notification && $notification->employee_id && $notification->employee->is_performing_avization
                ? $notification->employee->phone
                : ($warehouse && $warehouse->property ? $warehouse->property->phone : null);

            $amountOfMonits = App\MailReport::where('subject', 'like', '%Ponownie prosimy o potwierdzenie awizacji do%')->where('body', 'like', '%' . $or->id . '%')->count();

            $contactNotification = \App\Entities\OrderWarehouseNotification::where('order_id', $or->id)->where('contact_person', '!=', null)->first();
        @endphp

        <div class="timeline-item">
            <div class="timeline-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            </div>
            <div class="timeline-content">
                <h3 class="timeline-item-title">Warehouse Phone</h3>
                <p class="timeline-item-text">{{ $warehousePhone ?? 'Not provided' }}</p>
            </div>
        </div>

        <div class="timeline-item">
            <div class="timeline-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <div class="timeline-content">
                <h3 class="timeline-item-title">Notification Created</h3>
                <p class="timeline-item-text">{{ $notification?->created_at ?? 'Not available' }}</p>
            </div>
        </div>

        @if($warehouseMail)
            <div class="timeline-item">
                <div class="timeline-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                </div>
                <div class="timeline-content">
                    <h3 class="timeline-item-title">Warehouse Email</h3>
                    <p class="timeline-item-text">{{ strstr($warehouseMail, '@', true) }}@</p>
                </div>
            </div>

            @if($amountOfMonits > 0 && $hasLabel77)
                <div class="timeline-item">
                    <div class="timeline-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <div class="timeline-content">
                        <h3 class="timeline-item-title">Reminders Sent</h3>
                        <p class="timeline-item-text" style="color: #dc2626;">Sent {{ $amountOfMonits }} reminders regarding the notification</p>
                    </div>
                </div>
            @endif
        @endif

        <div class="timeline-item">
            <div class="timeline-icon">
                @if($notification && $notification->employee_id && $notification->employee->is_performing_avization)
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                @endif
            </div>
            <div class="timeline-content">
                <h3 class="timeline-item-title">Notification Handled By</h3>
                <p class="timeline-item-text">{{ $notification && $notification->employee_id && $notification->employee->is_performing_avization ? 'Employee' : 'Warehouse' }}</p>
            </div>
        </div>

        @if($contactNotification)
            <div class="timeline-item">
                <div class="timeline-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <div class="timeline-content">
                    <h3 class="timeline-item-title">Contact Person</h3>
                    <p class="timeline-item-text">{{ $contactNotification->contact_person }}</p>
                </div>
            </div>

            @if($contactNotification->contact_person_phone)
                <div class="timeline-item">
                    <div class="timeline-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <div class="timeline-content">
                        <h3 class="timeline-item-title">Contact Person Phone</h3>
                        <p class="timeline-item-text">{{ $contactNotification->contact_person_phone }}</p>
                    </div>
                </div>
            @endif
        @endif
    </div>
@endif
