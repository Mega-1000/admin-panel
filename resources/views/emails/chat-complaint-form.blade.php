<div>
    Dzień dobry,<br><br>
    prosimy zapoznac sie ze zgłoszniem reklamacyjnym i wypowiedzieć sie w tym temacie: <a href='{{ $url }}'>TUTAJ</a><br>
    Przyczyna: {{ $complaintForm->reason }}<br>
    Opis: {{ nl2br($complaintForm->description) }}<br>
    @if ($complaintForm->productValue)
        Wartość produktu: {{ $complaintForm->productValue }}<br>
    @endif
    @if ($complaintForm->damagedProductsValue)
        Wartość uszkodzonych produktów: {{ $complaintForm->damagedProductsValue }}<br>
    @endif
    Data: {{ $complaintForm->date }}<br>
    @if ($complaintForm->trackingNumber)
        Numer śledzenia: {{ $complaintForm->trackingNumber }}<br>
    @endif
    @if ($complaintForm->driverPhone)
        Telefon do kierowcy: {{ $complaintForm->driverPhone }}<br>
    @endif
    Pozdrawiamy,<br>
    administracja {{ config('app.name') }}
</div>
