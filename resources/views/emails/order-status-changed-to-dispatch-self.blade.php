<body>

<strong>Poniżej znajduję się zamówienie wraz z wszelkimi ustaleniami</strong>

<p>
    Prosimy zapoznać się z pełną treścią tego maila a następnie:
</p>
<ul>
    <li>Potwierdzić zanegować bądź przesunąć awizacje klikając na link: <a href="{{ $formLink }}">FORMULARZ
            POTWIERDZENIA ZAPRZECZENIA BĄDŹ PRZESUNIĘCIA AWIZACJI</a></li>
</ul>
<p style="color:red">W przeciwnym razie system będzie wysyłał co 15 minut informację z tą prośbą.</p>
<p>
    Także prosimy pozostawić tego maila do momentu wydania towaru który należy potwierdzić przyciskiem <a
        href="{{$sendFormInvoice}}">TOWAR ZOSTAŁ WYDANY</a>. w dniu wydania zlecenia.
</p>
<p>
    Prosimy pamiętać iz wszelkie dokumenty a w szczególności faktury dotyczące tego zlecenia można załączyć tylko i
    wyłącznie po użyciu przycisku <a href="{{$sendFormInvoice}}">ZAŁĄCZ DOKUMENT</a>.
</p>
<p>
    Jeżeli będzie to faktura to powinna byc bez pieczątek i podpisów i po wydrukowaniu powinna wyglądać jak oryginał.
    (Prosimy nie wysyłać poczta tradycyjna bo i tak nie dotrze do księgowości).
</p>
<p>
    Prosimy pamiętać iz respektujemy tylko i wyłącznie faktury w ten sposób załączone a wysłane poczta tradycyjna, nie
    zobowiązują nas do żadnych czynności księgowo finansowych na co wyrażacie Państwo zgodę poprzez zatwierdzenie
    awizacji.
</p>

<hr>
<hr>
<p>
    Nr oferty: {{$order->id}}
</p>
<p>
    Prosimy wpisywać nr oferty na wszystkich dokumentach ponieważ tylko po nim identyfikujemy zlecenia.
</p>
<p>
    {{$order->warehouse->symbol}}<br>
    {{$order->warehouse->address->address}} {{$order->warehouse->address->warehouse_number}}<br>
    {{$order->warehouse->address->postal_code}} {{$order->warehouse->address->city}}<br>
</p>
<p>
    !!!!!!! UWAGA W 2018 FAKTURUJEMY NA NOWA FIRMĘ DANE PONIŻEJ !!!!!!!!<br>
    Od<br>
    MEGA1000 BIS SP Z O O<br>
    ŻEROMSKIEGO 52/18<br>
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
    Informujemy iz pozostały proces informacyjny oraz omówienie wszelkich szczegółów dostawy oraz kontroli nad całym
    dalszym procesem dostawy pozostawimy po waszej stronie a wszelkie niedomówienia i przypadki losowe które wynikną w
    trakcie realizacji zobowiązujecie sie rozwiązać pomiędzy sobą a odbiorca towaru i nie obciążają nas w żaden sposób
    na co wyrażacie zgodę poprzez przyjecie awizacji. Jeżeli ceny naszego zamówienia sa niższe od cen które będziecie
    państwo wystawiać na fakturze to prosimy wstrzymać wypisywanie i wysłać info na e-maila i skontaktować sie z tel
    691801594.
    Faktury prosimy wysłać bezzwłocznie na adres zwrotny.
    Jeżeli faktura po wydrukowaniu będzie wyglądała jak oryginał to można nie wysyłac jej w formie papierkowej (dlatego
    nie moga byc podpisy albo pieczątki albo skan który zostawia smugi)
    Jeżeli natomiast faktura będzie wysyłana poczta to na adres MEGA1000 Baczyńskiego 32A 55-200 OŁAWA

</p>
<p>
    Z pozdrowieniami
    ZESPÓL MEGA1000
</p>
</body>
