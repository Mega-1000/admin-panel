Otrzymałeś wiadomość w związku z FAQ Allegro.

<br>
<br>

Parametry: |<br><br>
------------ |<br>
Nazwa użytkownika: {{ $user->name }} |<br>
Adres e-mail: {{ $user->login }} |<br>
Numer telefonu: {{ $user->phone }} |<br>
<a href="{{ route('customers.edit', $user->id) }}">
    Zobacz użytkownika
</a><br>
------------ |

<br>
<br>

Ścieżka do wiadomości: {{ $questionsTree }}

<br>
<br>

Tytuł wiadomości: {{ $messageText }}
