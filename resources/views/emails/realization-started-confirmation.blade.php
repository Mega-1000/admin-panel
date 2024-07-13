Dzień dobry,
<br>
<br>
Zauważyliśmy, że zamówienie o numerze {{ $order->id }} zostało zatwierdzone.
<br>
<br>
Prosimy o opłacenie zamówienia pod linkiem
<a href="https://mega1000.pl/payment?token={{ $order->token }}&total={{ $order->getValue() }}&credentials={{$order->customer->login}}:{{$order->customer->phone}}">
    FAKTURA PROFORMA
</a>
<br>
<br>
Przybliżony czas realizacji zamówienia to 7 dni roboczych. Jeśli potrzebujesz szybszej lub opóźnionej realizacji, prosimy o kontakt pod numerem 576 205 389.
<br>
<br>
Z Pozdrowieniami,
<br>
Zespół EPH Polska
