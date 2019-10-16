<body>

<strong>Poniżej znajduje się zamówienie wraz z wszelkimi ustaleniami</strong>

<p>
    Prosimy zapoznać się z pełną treścią tego maila a następnie:
</p>
<ul>
    <li>Potwierdzic zanegowac badz przesunac awizacje klikajac na link: <a href="{{ $formLink }}">FORMULARZ POTWIERDZENIA ZAPRZECZENIA BADZ PRZESUNIECIA AWIZACJI</a></li>
</ul>
<p style="color:red">W przeciwnym razie system bedzie wysyłał co 15 minut informację z tą prośbą.</p>
<p>
    Także prosimy pozostawić tego maila do mometnu wydania towaru który należy potwierdzić przyciskiem <a href="{{$sendFormInvoice}}">TOWAR ZOSTAŁ WYDANY</a>. w dniu wydania zlecenia.
</p>
<p>
    Proismy pamietac iz wszelkie dokumenty a w szczegolnosci faktury dotyczace tego zlecenia mozna zalaczyc tylko i wylacznie po uzyciu przyciksu <a href="{{$sendFormInvoice}}">ZALACZ DOKUMENT</a>.
</p>
<p>
    Jeżeli bedzie to faktura to powinna byc bez pieczatek i podpisow i po wydrukowaniu powinna wygladac jak oryginal. (Prosimy nie wysylac poczta tradycyjna bo i tak nie dotrze do ksiegowosci).
</p>
<p>
    Prosimy pamietac iz respektuejmy tylko i wyłącznie faktury w ten sposób zalaczone a wyslane poczta tradycyjna nie zobowiazuja nas do zadnych czynności ksiegowo finansowych na co wyrazacie państwo zgode poprzez zatwierdzenie awizacji.
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
    !!!!!!!  UWAGA W 2018 FAKTURUJEMY NA NOWA FIRME DANE PONIZEJ  !!!!!!!!<br>
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
    Data rozpoczęcia nadawania przesyłki: {{$order->shipment_date}}
</p>
<p>
    Informujemy iz pozostaly proces informacyjny oraz omowienie wszelkich szczegolow dostawy oraz kontroli nad calym dalszym procesem dostawy pozostawimy po waszej stronie a wszelkie niedomowienia i przypadki losowe ktore wynikna w trakcie realizacji zobowiazujecie sie rozwiazac pomiedzy soba a odbiorca towaru i nie obciazaja nas w zaden sposob na co wyrazacie zgode poprzez przyjecie awizacji.Jezeli ceny naszego zamowienia sa nizsze od cen ktore bedziecie panstwo wystawiac na fakturze to prosimy wstrzymac wypisywanie i wyslac info na e-maila i skontaktowc sie z tel 691801594.
    Faktury prosimy wyslac bezzwlocznie na adres zwrotny.
    Jezeli faktura po wydrukowaniu bedzie wygladala jak oryginal to mozna nie wysylac jej w formie papierkowej (dlatego nie moga byc podpisy albo pieczatki albo skan ktory zostawia smugi)
    Jezeli natomiast faktura bedzie wysylana poczta to na adres MEGA1000  Baczynskiego 32A 55-200 OLAWA

</p>
<p>
    Z pozdrowieniami
    ZESPOL MEGA1000
</p>
</body>
