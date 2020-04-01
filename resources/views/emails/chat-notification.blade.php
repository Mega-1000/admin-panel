<div>
    Dzień dobry,<br><br>
    masz nową wiadomość na platformie {{ env('APP_NAME') }}.<br>
    {{ $title }}<br>
    Kliknij <a href='{{ $url }}'>TUTAJ</a>, aby ją odczytać i odpowiedzieć.<br><br>
    Pozdrawiamy,<br>
    administracja {{ env('APP_NAME') }}
</div>