<body>
Proszę o anulowanie zlecenia o id: {{$package->order_id}}/{{$package->number}}<br>
Numer listu przewozowego: {{$package->letter_number}}<br>
Numer nadania: {{$package->sending_number}}<br>

<p>
    Jeśli wyrażacie Państwo zgodę na anulację - proszę o kliknięcie przycisku TAK, w innym przypadku proszę o kliknięcie
    przycisku NIE.
</p>
<p>
    <a href="{{$url}}?cancelled=true">Tak</a>
</p>
<p>
    <a href="{{$url}}?cancelled=false">Nie</a>
</p>
</body>
