Dzień dobry,
<br>
@php
$order = \App\Entities\Order::find($confirmation->order_id);
@endphp
<br>
Pod następującym linkiem dostępne jest potwierdzenie przelewu wykonanego przez nas: <a href="{{ $confirmation->file_url }}"> {{ $confirmation->file_url }} </a> do oferty o numerze: {{ $confirmation->order_id }}.
<br>
<br>
Prosimy o potwierdzenie otrzymania tej wiadomości klikając <a href="https://admin.mega1000.pl/create-confirmation/{{ \App\Entities\Order::find($confirmation->order_id)->id }}}}/confirm">TUTAJ</a>
<br>
<br>
W przypadku braku potwierdzenia odczytania tej wiadomości będziemy zmuszeni do ponownego wysyłania jej co 15 minut.
<br>
<br>
Pozdrawiamy,
<br>
Zespół EPH Polska
