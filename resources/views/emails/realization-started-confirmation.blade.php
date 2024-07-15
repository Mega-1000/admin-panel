Dzień dobry,
<br>
<br>
Zauważyliśmy, że zamówienie o numerze {{ $order->id }} zostało zatwierdzone.
<br>
<br>
Prosimy o opłacenie zamówienia pod linkiem
<a href="https://admin.mega1000.pl/order-proform-pdf/{{ $order->orderOffers->first()->id }}">
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
