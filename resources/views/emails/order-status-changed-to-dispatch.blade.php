<body>

<strong>Poniżej znajduje się zamówienie wraz z wszelkimi ustaleniami</strong>

<p>
    Prosimy zapoznać się z pełną treścią tego maila a następnie:
</p>
<ul>
    <li>Potwierdzić lub zanegować kliknając na link: <a href="{{ $formLink }}">FORMULARZ POTWIERDZANIA AWIZACJI</a></li>
</ul>
<p>
    W przypadku gdy awizazja nie zostanie potwierdzona bądź odrzucona, system będzie wysyłał informację z prośbą o
    wykonanie tej czynności.
</p>
<p style="color:red">Jeśli awizacja jest pozytywna pod różnymi warunkami np. zapłacenie proformy prosimy o ZATWIERDZENIE
    oraz wpisanie informacji o tym warunku w polu UWAGI.</p>
<p>
    Także prosimy pozostawić tego maila do mometnu wydania towaru który neleży potwierdzić przyciskiem <a
        href="{{$sendFormInvoice}}">TOWAR ZOSTAL WYDANY</a> w dniu wydania zlecenia.
</p>
<p>
    Jednoczesnie prosimy zalączyc fakturę a dokonać tego można po uzyciu przycisku <a
        href="{{$sendFormInvoice}}">ZALACZ FAKTURE</a>.
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
{{$order->employee->firstname . ' ' . $order->employee->lastname}}<br/>
{{$order->employee->email . ' ' . $order->employee->phone}}<br/>
{{'Numer konsultanta:' . $order->employee->name}}<br/>


<p>
    Od<br>
    ELEKTRONICZNA PLATFORMA HANDLOWA WOJCIECH WEISSBROT<br/>
    IWASZKIEWICZA 15A<br/>
    55-200 OLAWA<br/>
    NIP: 9121027907<br/>
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
