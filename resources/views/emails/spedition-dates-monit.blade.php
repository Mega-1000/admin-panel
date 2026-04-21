Dzień dobry,
<br>
<p>
    UWAGA !!<br>
            Od dnia 29.07.2024 dostęny jest panel wszystkich zamówień wraz z możliwością wypełnienia wszystkich potrzebnych nam danych
    <br>Jeśli chcesz udostępnić ten moduł do innego działu twojej firmy to skopiuj link i wyślij do odpowiedniej osobyJeśli chcesz udostępnić ten moduł do innego działu twojej firmy to skopiuj link i wyślij do odpowiedniej osoby
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
<br>
Prosimy o informacje na temat statusu wysyłki zamówienia o numerze: {{ $order->id }}
<br>
<br>
Jeśli zamówienie zostanie wysłane jutro prosimy o wypełnienie formularza pod linkiem: <a href="https://admin.mega1000.pl/orders/set-order-as-shipping-today/{{$order->id}}">{{ route('shippingToday', $order->id) }}</a>
<br>
<br>
Jeśli zamówienie nie zostanie wysłane jutro kliknij na ten link: <a href="https://admin.mega1000.pl/orders/set-order-as-not-shipping-today/{{$order->id}}">{{ route('notShippingToday', $order->id) }}</a>
<br>
<br>
Jeśli jeszcze nie masz dokładnych informacji na temat wysyłki tego zamówienia prosimy zachować tę wiadomość i wybrać odpowiednią opcję w momencie otrzymania tej informacji (do godziny 13).
<br>
<br>
Z Pozdrowieniami,
<br>
EPH Polska
