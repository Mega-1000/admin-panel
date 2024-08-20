Dzień dobry,
<br>
<br>
Chcieli byśmy przypomnieć, że oferta przetargowa o numerze: {{ $order->id }} jest dostępna w naszym systemie.
<br>
<br>
Jeśli chcą Państwo złożyć zamówienie na jedną z ofertę prosimy o kliknięcie przycisku "Wyślij zamówienie na tego producenta" na tabeli cen.
<br><br>

<a href="https://admin.mega1000.pl/auctions/{{ $order->chat->auctions?->first()->id }}/end" style="display: inline-block; padding: 10px 20px; font-size: 16px; color: white; background-color: #007bff; text-align: center; text-decoration: none; border-radius: 5px; transition: background-color 0.3s ease;">
    Tabela cen zaproponowanych w przetargu
</a>

<br>
<br>

Jeśli znaleźli Państwo lepszą ofertę niż nasza prosimy o kontakt pod numerem 576 205 389 a my nie tylko zrównamy się z nią a także wynagrodzimy Państwa dodatkową zniżką w wysokości 100 zł.

<br>
<br>

Jeśli zapytanie nie jest aktualne prosimy wcisnąć przysisk poniżek

<a href="https://admin.mega1000.pl/auction-not-active/{{ $order->id }}" style="display: inline-block; padding: 10px 20px; font-size: 16px; color: white; background-color: red; text-align: center; text-decoration: none; border-radius: 5px; transition: background-color 0.3s ease;">
    Zapytanie nie jest aktuale
</a>

<br>
<br>
Z pozdrowieniami <br>
Zespół EPH Polska
