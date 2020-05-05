<body>
Szczegóły spedycji
Zlecenie transportowe nr: {{ $order->id }}

Przybliżona waga transportu: 0 kg

Dostawa
W celu możliwości rozładunku towaru prosimy skontaktować się z odbiorcą lub odbiorcami, których dane podane są poniżej.

Jednocześnie informujemy, iż wszelkie ustalenia co do zasad transportu pozostawimy pomiędzy spedytorem a odbiorcą końcowym i w przypadku wystąpienia problemów z tym związanych obciążają całkowicie stronę spedytora, a wszelkie rozwiązania będzie on ustalał z odbiorcą końcowym z pominięciem firmy MEGA1000

Spedytor w szczególności jest zobowiazany ustalić szczegóły i możliwości dojazdu, rozładunku towaru i pobrania należności za towar oraz za transport jeżeli taka wystepuje.

Dane odbiorcy:

Zlecenie nr: {{ $order->id  }}

{{ $order->addresses[0]->firstname }} {{ $order->addresses[0]->lastname }}

{{ $order->addresses[0]->address }} {{ $order->addresses[0]->flat_number }}

{{ $order->addresses[0]->postal_code }}

{{ $order->addresses[0]->city }}

{{ $order->addresses[0]->phone }}

{{ $order->addresses[0]->email }}

Załadunek
{{ $order->warehouse->symbol }}

{{ $order->warehouse->address->address }} {{ $order->warehouse->address->warheouse_number }}, {{ $order->warehouse->address->postal_code }}
{{ $order->warehouse->address->city }} {{ $order->warehouse->property->firstname }} {{ $order->warehouse->property->lastname }}
{{ $order->warehouse->property->phone }}
{{ $order->warehouse->property->comments }}

Specyfikacja zlecenia:
Waga zlecenia: {{ $order->weight }} kg

Wartość za usługe transportową brutto: {{ $order->shipment_price_for_us }} zł

Zleceniodawca tego transportu jest odbiorca towaru i wszelkie ustalenie co do zasad dostawy,rozładunku,terminu itp spedycja obsługująca ustala z nim we własnym zakresie a wszelkie niedomowienia i spory które wynikną w zwiazku z tą dostawą spedycja będzie rozwiązywała samodzielnie z odbiorcą z pominięciem firmy MEGA1000 który jest tylko sprzedawcą.Także ustalenia zasad płatności jak i wartości za wykonaną usługe prosimy uscislić z odbiorcą ponieważ on jest decydentem co do wszelkich aspektow finansowych poniewaz podawane przez nas dane są zawsze tylko wstepne.

Uwagi spedycji:
Do pobrania przez spedycję:
{{ $order->speditionPayments->sum('amount') }} zł


<h2>Zgłoszenie spedycji</h2>
<p>Poprzez wypełnienie i odesłanie danych do tego zlecenia spedycja przyjmuje wyżej wymienione zasady i
    bierze odpowiedzialność za prawidłowe ich wykonanie oraz pobrany towar.</p>
<p>Ostateczne zatwierdzenie wyboru państwa firmy zostanie potwierdzone drogą e-mailową w przeciągu kilku
    minut.</p>
</body>
