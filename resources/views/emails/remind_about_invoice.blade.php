Dzień dobry,
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
