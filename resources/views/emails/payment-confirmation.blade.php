Dzień dobry,
<br>
<br>
Pod następującym linkiem dostępne jest potwierdzenie przelewu wykonanego przez nas: <a href="{{ $confirmation->file_url }}"> {{ $confirmation->file_url }} </a>
<br>
<br>
Prosimy o potwierdzenie otrzymania przelewu klikając <a href="{{ route('store-payment-confirmation-confirm', \App\Entities\Order::find($confirmation->order_id)->id) }}">TUTAJ</a>
