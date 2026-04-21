<div>
    Dzień dobry,<br><br>
    masz nową wiadomość na platformie {{ config('app.name') }}.<br>
    {{ $title }}<br>
    Kliknij <a href='{{ $url }}'>TUTAJ</a>, aby ją odczytać i odpowiedzieć.<br>
    Lub skopiuj link do adresu przeglądarki: <br>
    {{ $url }}
    <br><br>
    Pozdrawiamy,<br>
    administracja {{ config('app.name') }}
</div>
