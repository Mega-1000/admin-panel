<body>
@if($path !== null)
    <strong>W załączniku przesyłamy protokół nadania paczki.</strong>
@endif

<p>
    UWAGA !!<br>
    Od dnia 29.07.2024 dostęny jest panel wszystkich zamówień wraz z możliwością wypełnienia wszystkich potrzebnych nam danych
    <br>
    <a
        href="https://admin.mega1000.pl/firm-panel-actions/{{ \App\Entities\Firm::where('symbol', $order->items->first()->product->manufacturer)->first()->id }}"
        style="background-color: #4CAF50; /* Green */
        border: none;
        color: white;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 12px;
        padding: 10px 24px;"
    >PANEL WSZYSTKICH ZAMÓWIEN DO WASZEJ FIRMY</a>
</p>

<p>
    Wyglada na to iż powiniście w dniu dzisiejszym rozpoczać nadawanie przesyłki o numerze {{$packageNumber}} prosimy w
    przypadku wydania w dniu dzisieszym prosimy o potwierdzenie wydania towaru a jeżeli termin rozpoczęcia bedzie
    przesunięty to prosimy ustalic to z odbiorcą i wskazać nową datę dostarczenia przesyłki.
</p>
<ul>
    <li>W przypadku w którym wydanie paczki nastąpi dzisiaj prosimy o zaznaczenie tej informacji w naszym formularzu: <a href="{{ $sendFormInvoice }}">FORMULARZ POTWIERDZANIA AWIZACJI</a></li>
</ul>
<p style="color:red">W przeciwnym razie system bedzie wysyłał co 30 minut informację z tą prośbą.</p>
<p>
    Jednoczesnie prosimy zalączyc fakturę a dokonać tego można po uzyciu przycisku ZALACZ FAKTURE.
</p>
<p>
    Faktura powinna byc bez pieczatek i podpisow i po wydrukowaniu powinna wygladac jak oryginal. (Prosimy nie wysylac
    poczta tradycyjna bo i tak nie dotrze do ksiegowosci).
</p>
<p>
    Prosimy pamietac iz respektuejmy tylko i wyłącznie faktury w ten sposób zalaczone a wyslane poczta tradycyjna nie
    zobowiazuja nas do zadnych czynności na co wyrazacie państwo zgode poprzez zatwierdzenie awizacji.
</p>
<hr>
<hr>
<p>
    Nr oferty: {{$order->id}}
</p>
<p>
    Prosimy wpisywac nr oferty na wszystkich dokumentach poniewaz tylko po nim identyfikujemy zlecenia.
</p>
<p>
    {{$order->warehouse->symbol}}<br>
    {{$order->warehouse->address->address}} {{$order->warehouse->address->warehouse_number}}<br>
    {{$order->warehouse->address->postal_code}} {{$order->warehouse->address->city}}<br>
</p>
<p>
    !!!!!!! UWAGA W 2018 FAKTURUJEMY NA NOWA FIRME DANE PONIZEJ !!!!!!!!<br>
    Od<br>
    MEGA1000 BIS SP Z O O<br>
    ZEROMSKIEGO 52/18<br>
    50-112 WROCLAW<br>
    NIP 8971719229<br>
</p>
<p>
    Dane do dostawy:
    {{$order->addresses->first->id->firstname}} {{$order->addresses->first->id->lastname}}<br>
    {{$order->addresses->first->id->address}} {{$order->addresses->first->id->flat_number}}<br>
    {{$order->addresses->first->id->postal_code}} {{$order->addresses->first->id->city}}<br>
    Tel: {{$order->addresses->first->id->phone}}<br>
</p>
<p>
    Uwagi osoby zamawiającej:<br>
    {{$order->customer_notices}}
</p>
<p>
    Uwagi do spedycji:<br>
    {{$order->consultant_notices}}
</p>
@foreach($order->items as $item)
    <p>
        Nazwa: {{$item->product->name}}<br>
        Symbol: {{$item->product->symbol}}<br>
        Ilość: {{$item->quantity}}<br>
        Cena jednostkowa netto: {{$item->net_purchase_price_commercial_unit}}<br>
        Wartość: {{$item->price}}<br>
    </p>
@endforeach
<p>
    Koszt transportu brutto: {{$order->shipment_price_for_client}}<br>
    Kwota ktora zobowiazujecie sie pobrac przed rozladunkiem i nam przekazac: {{$order->total_price}}<br>
    Data rozpoczęcia nadawania przesyłki: {{$order->shipment_date}}
</p>
<p>
    Z naszej strony kontakt z klientem sie konczy pozostaly proces informacji pozostawiamy po waszej stronie
    Jezeli ceny naszego zamowienia sa nizsze od cen ktore bedziecie panstwo wystawiac na fakturze to prosimy wstrzymac
    wypisywanie i,wyslac info na e-maila i skontaktowc sie 691801594
    Faktury prosimy wyslac bezzwlocznie na adres zwrotny.
    Jezeli faktura po wydrukowaniu bedzie wygladala jak oryginal to mozna nie wysylac jej w formie papierkowej (dlatego
    nie moga byc podpisy albo pieczatki albo skan ktory zostawia smugi)
    Jezeli natomiast faktura bedzie wysylana poczta to na adres MEGA1000 Baczynskiego 32A 55-200 OLAWA

</p>
<p>
    Z pozdrowieniami
    ZESPÓŁ EPH POLSKA
</p>
</body>
