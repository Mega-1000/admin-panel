<body>
<p>
    <strong>FORMULARZ AWIZACYJNY</strong>
</p>
<p>
    Prosimy nie odpisywać na tego e maila poprzez odpowiedź nadawcy i wpisując tekst w dowolne miejsce ponieważ nikt tego nie odczyta.
</p>
<p>
    Prosimy o zatwierdzenie lub zanegowanie wysłanej awizacji, a dokonać tego można na 2 sposoby.
</p>
<p>Pierwszym sposobem jest odpowiedź poprzez użycie poniżej wysłanych linków.<br>
Drugim jest przesłanie informacji jako e-mail zwrotny aczkolwiek aby informacja przesłana została odczytana przez nas poprawnie należy wpisać tekst w odpowiednim miejscu przez nas poniżej wskazanym.</p>
<p>
    Szybszym i prostszym sposobem jest odpowiedź poprzez użycie linków aczkolwiek wybór pozostawiamy państwu.<br>
    Brak odpowiedzi do 2 godzin na tego e-maila powoduje wysyłanie go co 15 minut jako przypomnienie.
</p>
<br>
<p>
    <strong>SPOSÓB NR I</strong>
</p>
<p>
    poprzez uzycie linków awizacyjnych
</p>
<p>Prosimy zapoznać się z pełną treścią tego maila a następnie:</p>

<p>
    Potwierdzić lub zanegować klikając na link: <a href="{{ $formLink }}">FORMULARZ POTWIERDZANIA AWIZACJI</a>
</p>
<p>
    Jeżeli awizacja jest pozytywna pod różnymi warunkami np zapłacenie proformy to prosimy zatwierdzić ją a w polu uwagi wpisać zatwierdzona pod warunkiem opłacenia faktury proformy do dnia , itp.itd.
</p>
<p>W przypadku odrzucenia awizacji należy podać koniecznie przyczyne.</p>
<p>
    Także prosimy pozostawić tego maila do mometnu wydania towaru który neleży potwierdzić przyciskiem <a href="{{$sendFormInvoice}}">TOWAR ZOSTAL WYDANY</a> w dniu wydania zlecenia.
</p>
<p>
    Jednoczesnie prosimy załączyć fakturę, a dokonać tego można po użyciu przycisku <a href="{{$sendFormInvoice}}">ZAŁĄCZ FAKTURĘ</a>.
</p>
<p>
    Faktura powinna byc bez pieczątek i podpisów i po wydrukowaniu powinna wyglądać jak oryginał. (Prosimy nie wysyłać pocztą tradycyjną bo i tak nie dotrze do księgowości).
</p>
<p>
    Prosimy pamietać iż respektujemy tylko i wyłącznie faktury w ten sposób załączone a wysłane pocztą tradycyjną nie zobowiazują nas do żadnych czynności na co wyrażacie
    Państwo zgodę poprzez zatwierdzenie awizacji.
</p>
<p>
    !!! W przypadku gdybyście chcieli poinformować klienta lub ustalić dowolne informacje to prosimy wpisać wiadomość/zapytanie na chacie który można uruchomić po kliknieciu na linka<br>
    <a href="{{ $chatLink }}">Link do chatu</a>
</p>
<br><br>
<p>
    <strong>SPOSÓB NR II</strong>
</p>
<p>bardziej skomplikowany aczkolwiek nie wymaga użycia linków</p>
<br><p>
    <strong>1A) POTWIERZENIE LUB ODRZUCENIA AWIZACJI</strong>
</p>
<p>Poniżej po dwukropku należy wpisać cyfrę 1 dla prawidłowego wyboru:</p>
<p>Akceptuję awizacje z podanymi parametrami (AAZPP): </p>
<p>Odrzucam awizacje z podanymi parametrami (OAZPP): </p>
<p>
    W uwagach na dole można dokonać opisu miedzy innymi wiadomości powiązanych z tematami poniżej.<br>
    Jeżeli awizacja jest pozytywna pod różnymi warunkami np. zapłacenie proformy to prosimy zatwierdzić ją a w polu uwagi wpisać zatwierdzona pod warunkiem opłacenia faktury proformy do dnia , itp.itd.<br>
    W przypadku odrzucenia awizacji należy podać koniecznie przyczyne w uwagach na dole.<br>
</p>
<br><p>
    <strong>1B) Wstępne zaproponowane daty możliwości odbioru towaru przez klienta (podajemy tylko informacyjnie)</strong>
</p>
<p>od: {{ $customerShipmentDateFrom }}</p>
<p>do: {{ $customerShipmentDateTo }}</p>
<br>
<p>Prosimy poniżej we wszystkich miejscach po dwukropku precyzyjnie uzupełnić dane:</p>
<br><p><strong>1C) DATY DOSTAWY OD DO KONIECZNIE W FORMACIE 2022-10-15</strong></p>
<p>od daty (DOOF): </p>
<p>do daty (DODF): </p>
<br>
<p><strong>1D) OSOBA ODPOWIEDZIALNA ZA OBSŁUGĘ</strong></p>
<p>Imię i Nazwisko (OOZO): </p>
<p>Nr telefonu (NTOOZO): </p>
<br>
<p><strong>1E) KONTAKT DO KIEROWCY</strong></p>
<p>
    Jeżeli na ten moment dane dostawcy/kierowcy nie są znane, to prosimy je uzupełnić w późniejszym czasie jeżeli to będzie możliwe.
</p>
<p>Numer telefonu do kierowcy (NTDK): </p>
<br>
<p>
    <strong>1E) UWAGI</strong>
</p>
<p>Uwagi (U): </p>
<br>
<p>
    <strong>1F) KOMUNIKACJA Z KLIENTEM</strong>
</p>
<p>
    W przypadku gdybyście chcieli poinformować klienta lub ustalić dowolne informacje to prosimy wpisać wiadomość/zapytanie
</p>
<p>(AWDK): </p>
<br>
<p>
    <strong>2A) INFORMACJA W MOMENCIE WYDANIA TOWARU</strong>
</p>
<p>Po wydaniu towaru prosimy także o informacje zwrotną że towar został wydany,
    należy tego dokonać poprzez wpisanie między znakiem dwukropka cyfry 1
</p>
<p>Towar został wydany (TZW): </p>
<br>
<p>
    <strong>3A) PODŁĄCZENIA DOKUMENTÓW</strong>
</p>
<p>
    Faktury oraz inne dokumenty można załączyć poprzez dodanie jako załączniki do tego e-maila dowolną ich ilość, która zostanie automatycznie podpięta do tego zamówienia
</p>
<p>
    Prosimy koniecznie na każdym dokumencie powiązanym z tą ofertą wpisywać numer oferty czyli: {{$order->id}}
</p>
<hr>
<hr>
<p>PARAMETRY ZAMÓWIENIA</p>
<p>
    Nr oferty: {{$order->id}}
</p>
<p>
    Prosimy wpisywać nr oferty na wszystkich dokumentach ponieważ tylko po nim identyfikujemy zlecenia.
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

<p>Z naszej strony kontakt z klientem się kończy pozostały proces informacji pozostawiamy po waszej stronie</p>
<p>
    Jeżeli ceny naszego zamówienia sa niższe od cen które będziecie Państwo wystawiać na fakturze to prosimy o odrzucenie potwierdzenia i opisanie tego w uwagach.
</p>
<br><br>
<p>ZAWARTOŚĆ ZAMÓWIENIA WRAZ Z PODSUMOWANIEM WARTOŚCI</p>
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
    Koszt transportu dla nas brutto: {{$order->shipment_price_for_us}}<br>
    @if($toPay > 2)
        Kwota ktora zobowiazujecie sie pobrac przed rozladunkiem i nam
        przekazac: {{$toPay}} zł<br>
    @endif
    Data rozpoczęcia nadawania przesyłki: {{$order->shipment_date}}
</p>
<br>
<p>
    Z pozdrowieniami
    ZESPOL MEGA1000
</p>
</body>