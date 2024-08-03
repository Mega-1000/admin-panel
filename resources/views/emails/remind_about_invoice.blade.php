Dzień dobry,
<br>
<br>
UWAGA !!<br>
Od dnia 29.07.2024 dostęny jest panel wszystkich zamówień wraz z możliwością wypełnienia wszystkich potrzebnych nam danych
<br>Jeśli chcesz udostępnić ten moduł do innego działu twojej firmy to skopiuj link i wyślij do odpowiedniej osobyJeśli chcesz udostępnić ten moduł do innego działu twojej firmy to skopiuj link i wyślij do odpowiedniej osoby
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
<br>
<br>
Prosimy o podpięcie faktury do zamówienia o numerze: {{ $order->id }}.
<br>
<br>
Aby podpiąć fakturę do naszego systemu kliknij w poniższy link:
<a href="{{ rtrim(config('app.front_nuxt_url'), "/") . "/magazyn/awizacja/{$order->orderWarehouseNotifications->first()->id}/{$order->warehouse_id}/{$order->id}/wyslij-fakture" }}" class="btn btn-primary" style="color: #fff; text-decoration: none; padding: 10px 20px; background-color: #007bff; border-radius: 5px; display: inline-block; margin-top: 20px;">
    Podpięcie faktury do zamówienia
</a>
<br>
<br>
Pozdrawiamy,
<br>
Zespół EPH Polska
