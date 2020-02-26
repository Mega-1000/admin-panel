<body>
<br/><br/>
Data: {{ $date }} <br/>
Wysłaliśmy Ci informacje do zamówienia nr: {{ $orderId }} <br/><br/>

Uwaga! Ten e-mail jest generowany automatycznie. Aby odpowiedzieć na tą wiadomość lub przekazać dalsze informacje prosimy zalogować się na swoim koncie <br/>
( W przeciwnym wypadku wiadomość może być pominięta ): <br/><br/>

Aby rozpocząć dialog w sprawie zamówienia: {{ $orderId }}, przejdź pod adres: https://{{env('APP_URL')}}/communication/{{ $orderId }}

</body>
