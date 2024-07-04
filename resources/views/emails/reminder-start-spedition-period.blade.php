Szanowni Państwo,
<br>
<br>
Przypominamy o tym, że przedział dat wysyłki dla zamówienia o id {{ $order->id }} rozpoczyna się jutro.

<br>
<br>


Jeśli zamówienie juź wyjechało, prosimy o potwierdzenie tego faktu klikając w przycisk poniżej:
<a href="{{ rtrim(config('app.front_nuxt_url'), '/') . "/magazyn/awizacja/{$order->orderWarehouseNotifications->first()->id}/$order->warehouse_id/$order->id/wyslij-fakture" }}">
    <button style="background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;">Towar został wydany</button>
</a>

<br>
<br>

Z pozdrowieniami, <br>
Zespół EPH Polska
