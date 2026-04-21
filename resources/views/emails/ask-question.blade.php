<body>
<br/>
Klient: {{ $firstName }} {{ $lastName }}<br/>
Pytanie: {{ $details }} <br/>
Numer telefonu do kontaktu: {{ $phone }}<br/>
Pytanie wysłane dnia: {{ $date->format('Y-m-d') }}
</body>
