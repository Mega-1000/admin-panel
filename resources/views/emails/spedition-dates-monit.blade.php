Dzień dobry,
<br>
<br>
Prosimy o informacje na temat statusu wysyłki zamówienia o numerze: {{ $order->id }}
<br>
<br>
Jeśli zamówienie zotanie wysłane jutro prosimy o wypełnienie formularza pod linkiem: <a href="{{ route('shippingToday', $order->id) }}">{{ route('shippingToday', $order->id) }}</a>
<br>
<br>
Jeśli zamówienie nie zostanie wysłane jutro kliknij na ten link: <a href="{{ route('notShippingToday', $order->id) }}">{{ route('notShippingToday', $order->id) }}</a>
<br>
<br>
<br>
Z Pozdrowieniami,
<br>
EPH Polska
