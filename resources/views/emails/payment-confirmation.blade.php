Dzień dobry,
<br>
@php
$order = \App\Entities\Order::find($confirmation->order_id);
@endphp
<p>
    UWAGA !!<br>
        Jeśli chcesz udostępnić ten moduł do innego działu twojej firmy to skopiuj link i wyślij do odpowiedniej osoby
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
