Dzień dobry,
<br>
<br>
Pod następującym linkiem dostępne jest potwierdzenie przelewu wykonanego przez nas: <a href="{{ $confirmation->file_url }}"> {{ $confirmation->file_url }} </a> do oferty o numerze: {{ $confirmation->order_id }}.
<br>
<br>
Prosimy o potwierdzenie otrzymania tej wiadomości klikając <a href="{{ route('store-payment-confirmation-confirm', \App\Entities\Order::find($confirmation->order_id)->id) }}">TUTAJ</a>
<br>
<br>
W przypadku braku potwierdzenia odczytania tej wiadomości będziemy zmuszeni do ponownego wysyłania jej co 15 minut.
<br>
<br>
Pozdrawiamy,
<br>
Zespół EPH Polska
