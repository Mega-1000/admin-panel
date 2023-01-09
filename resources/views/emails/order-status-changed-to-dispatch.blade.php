<body>

<p>
    Prosimy nie odpisywać informacji poprzez napisanie e maila jako tekst w dowolnym miejscu ponieważ nikt tego nie odczyta.
    Pierwszym sposobem jest odpowiedź poprzez użycie poniżej wysłanych linków.
    Drugim jest przesłanie informacji jako e mail aczkolwiek aby informacja przesłana została odczytana przez nas poprawnie należy wpisać tekst w odpowiednim miejscu przez nas poniżej wskazanym i dac odpowiedz nadawcy.
    Można także stworzyć włanego emaila aczkolwiek wtedy należy skopiować dokładnie tekst poniżej zawarty i go uzupełnić i wysłać na awizacje@ephpolska.pl
    Szybszym o prostszym sposobem jest odpowiedź poprzez użycie linków aczkolwiek wybór pozostawiamy państwu.
    Brak odpowiedzi do 2 godzin na tego emaila powoduje wysyłanie go co 15 minut jako przypomnienie.
</p>


<strong>SPOSÓB NR I - poprzez uzycie linków awizacyjnych</strong>

<p>Prosimy zapoznać się z pełną treścią tego maila a następnie następnie:</p>

<p>
    Potwierdzić lub zanegować kliknając na link: <a href="{{ $formLink }}">FORMULARZ POTWIERDZANIA AWIZACJI</a>
</p>
<p style="color:red">
    Jeżeli awizacja jest pozytywna pod różnymi warunkami np zapłacenie proformy to prosimy zatwierdzić
    ją a w polu uwagi wpisać zatwierdzona pod warunkiem opłacenia faktury proformy do dnia , itp.itd.
</p>
<p>W przypadku odrzucenia awizacji należy podać koniecznie przyczyne.</p>
<p>
    Także prosimy pozostawić tego maila do mometnu wydania towaru który neleży potwierdzić przyciskiem <a
        href="{{$sendFormInvoice}}">TOWAR ZOSTAL WYDANY</a> w dniu wydania zlecenia.
</p>
<p>
    Jednoczesnie prosimy załączyc fakturę a dokonać tego można po użyciu przycisku
    <a href="{{$sendFormInvoice}}">ZALACZ FAKTURE</a>.
</p>
<p>
    Faktura powinna byc bez pieczątek i podpisów i po wydrukowaniu powinna wyglądać jak oryginał. (Prosimy nie wysyłać pocztą tradycyjną bo i tak nie dotrze do księgowości).
</p>
<p>
    Prosimy pamietać iż respektujemy tylko i wyłącznie faktury w ten sposób załączone a wysłane pocztą tradycyjną nie zobowiazują nas do żadnych czynności na co wyrażacie
    Państwo zgodę poprzez zatwierdzenie awizacji.
</p>
<p>
    !!! W przypadku gdybyście chcieli poinformować klienta lub ustalić dowolne informacje to
     prosimy wpisać wiadomość/zapytanie na chacie który można uruchomić po kliknieciu na linka
     [ link do chata klienta ktory uruchomi go przy danej ofercie oraz z uczestnikami klient , my czyli konsultanci EPH oraz magazyn obsługujący )
</p>

<strong>SPOSOB NR II - bardziej skomplikowany aczkolwiek nie wymaga użycia linków</strong>

<strong>1A) POTWIERZENIE LUB ODRZUCENIA AWIZACJI</strong>

<p>Poniżej dwukropkiem a średnikiem należy wpisać cyfrę 1 dla prawidłowego wyboru:</p>

<p>Akceptuję awizacje z podanymi parametrami (AAZPP): ;</p>
<p>Odrzucam awizacje z podanymi parametrami (OAZPP): ;</p>

<p>
    W uwagach na dole można dokonać opisu miedzy innymi wiadomości powiązanych z tematami poniżej
    Jeżeli awizacja jest pozytywna pod różnymi warunkami np zapłacenie proformy to prosimy zatwierdzić ją a w polu uwagi wpisać zatwierdzona pod warunkiem opłacenia faktury proformy do dnia , itp.itd.
    W przypadku odrzucenia awizacji należy podać koniecznie przyczyne w uwagach na dole.
</p>

<strong>1B) Wstępne zaproponowane daty możliwości odbioru towaru przez klienta podajemy tylko informacyjnie</strong>
<p>od (WDOO): ;</p>
<p>do (WDOD): ;</p>
<br>
<p>Prosimy poniżej we wszytkich miejscach między dwukropkiem a średnikiem precyzyjnie uzupełnić dane.</p>
<strong>1C) DATY DOSTAWY OD DO KONIECZNIE W FORMACIE 2022-10-15</strong>
<p>od daty (WDOO): ;</p>
<p>do daty (WDOD): ;</p>
<br>
<strong>1D) OSOBA ODPOWIEDZIALNA ZA OBSŁUGĘ </strong>
<p>Imię i Nazwisko (OOZO): ;</p>
<p>Nr telefonu (NTOOZO): ;</p>
<br>
<strong>1E) KONTAKT DO KIEROWCY</strong>
<p>
    Jeżeli na ten moment dane dostawcy/kierowcy nie są znane, to prosimy je uzupełnić w późniejszym czasie jeżeli to będzie możliwe.
</p>
<p>Numer telefonu do kierowcy (NTDK): ;</p>
<br>
<strong>1E) UWAGI</strong>
<p>Uwagi (U): ;</p>
<br>
<strong>1F) KOMUNIKACJA Z KLIENTEM</strong>
<p>
    W przypadku gdybyście chcieli poinformować klienta lub ustalić dowolne informacje to prosimy wpisać wiadomość/zapytanie
    (AWDK): ;
</p>
<p>
    Wiadomość ta należy wpisac do chata znajdującego sie w danej ofercie z uczestnikami klient, my czyli konsultanci EPH oraz magazyn obsługujący
</p>
<br>
<strong>2A) INFORMACJA W MOMENCIE WYDANIA TOWARU</strong>
<p>Po wydanu towaru prosimy takżę o informacje zwrotną że towar został wydany -
    należy tego dokonać poprzez wpisanie między znakiem dwukropka a średnika cyfrę 1
</p>
<p>Towar zostały wydany (TZW): ;</p>
<br>
<strong>3A) PODŁĄCZENIA DOKUMENTÓW</strong>
<p>
    Faktury oraz inne dokumenty można załączyć poprzez dodanie jako załączniki
    do tego e-maila dowolną ich ilość która zostanie automatycznie podpięta do tego zamówienia
</p>
Prosimy koniecznie na każdym dokumencie powiązanym z tą ofertą wpisywać numer oferty czyli: {{$order->id}}
<hr>
<hr>
<p>
    Nr oferty: {{$order->id}}
</p>
<p>
    Prosimy wpisywac nr oferty na wszystkich dokumentach poniewaz tylko po nim identyfikujemy zlecenia.
</p>
@isset($order->employee)
    {{$order->employee->firstname . ' ' . $order->employee->lastname}}<br/>
    {{$order->employee->email . ' ' . $order->employee->phone}}<br/>
    {{'Numer konsultanta:' . $order->employee->name}}<br/>
@endisset


<p>
    Od<br>
    ELEKTRONICZNA PLATFORMA HANDLOWA SP. Z O.O.<br/>
    JARACZA 22/12<br/>
    50-305 WROCAŁAW<br/>
    NIP: 8982272269<br/>
</p>
<p>
    Dane do dostawy: <br/>
    {{$order->addresses->first->id->firstname}} {{$order->addresses->first->id->lastname}}<br>
    {{$order->addresses->first->id->address}} {{$order->addresses->first->id->flat_number}}<br>
    {{$order->addresses->first->id->postal_code}} {{$order->addresses->first->id->city}}<br>
    Tel: {{$order->addresses->first->id->phone}}<br>
</p>
<p>
    @if($order->customer_notices)
        Uwagi osoby zamawiającej: <br/>
        {{$order->customer_notices}}
    @endif
</p>
<p>
    @if($order->consultant_notices)
        Uwagi do spedycji: <br/>
        {{$order->consultant_notices}}
    @endif
</p>
@foreach($order->items as $item)
    <p>
        Nazwa: {{$item->product->name}} Symbol: {{$item->product->symbol}} <br/>
        {{$item->product->supplier_product_name ? 'Nazwa producenta: ' . $item->product->supplier_product_name : null}}
        {{$item->product->supplier_product_symbol ? 'Symbol producenta: ' . $item->product->supplier_product_symbol : null}}
    </p>
    @include('emails.item-price-table', [
    'item' => $item,
    'bw' => $item->product->packing->number_of_sale_units_in_the_pack,
    'bx' => $item->product->packing->number_of_trade_items_in_the_largest_unit,
    ])
    <br/>
@endforeach
@php
    $sumOfItems = 0;
    foreach ($order->items as $item) {
        $sumOfItems += ($item->gross_selling_price_commercial_unit * $item->quantity);
    }
    $orderValue = str_replace(',', '', number_format($sumOfItems + $order->shipment_price_for_client + $order->additional_service_cost + $order->additional_cash_on_delivery_cost, 2));
    $paymentsValue = 0;
    $paymentsPromise = 0;
    foreach($order->bookedPayments($order->id) as $payment){
        $paymentsValue += $payment->amount;
    }
    foreach($order->promisePayments($order->id) as $payment){
        $paymentsPromise += $payment->amount;
    }
    $toPay = $orderValue - $paymentsValue - $paymentsPromise;
@endphp
<p>
    @lang('orders.form.shipment_price_for_us'): {{$order->shipment_price_for_us}}<br>
    @if($toPay > 2)
        Kwota ktora zobowiazujecie sie pobrac przed rozladunkiem i nam
        przekazac: {{$toPay}} zł<br>
    @endif
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
    ZESPOL MEGA1000
</p>
</body>
    