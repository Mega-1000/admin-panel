Dzień dobry,
<br>
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
