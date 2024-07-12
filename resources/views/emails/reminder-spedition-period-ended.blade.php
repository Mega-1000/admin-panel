Szanowni Państwo,
<br>
<br>
Przypominamy o tym, że przedział dat wysyłki dla zamówienia o id {{ $order->id }} właśnie się zakończył.

<br>
<br>

Jeśli zamówienie juź wyjechało, prosimy o potwierdzenie tego faktu klikając w przycisk poniżej:
<a href="{{ rtrim(config('app.front_nuxt_url'), '/') . "/magazyn/awizacja/{$order->orderWarehouseNotifications->first()->id}/{$order->warehouse_id}/{$order->id}/wyslij-fakture" }}">
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
Potrzebujesz przełożyć daty zamówienia? Skontaktuj się z klientem po czym zaaktualizuj daty pod tym linkiem:

@php
    $lowestDistance = PHP_INT_MAX;
    $company = $order->warehouse->firm;
    $closestEmployee = null;

    foreach ($company->employees as $employee) {
    $employee->distance = App\Helpers\LocationHelper::getDistanceOfClientToEmployee($employee, $order->customer);

    if ($employee->distance < $lowestDistance) {
        $lowestDistance = $employee->distance;
            $closestEmployee = $employee;
        }
    }

    App\Services\MessageService::createNewCustomerOrEmployee($order->chat, new Illuminate\Http\Request(['type' => 'Employee']), $closestEmployee);


    $token = app(\App\Helpers\MessagesHelper::class)->getChatToken(
        $order->id,
        $closestEmployee->id,
        'e',
    );
@endphp
<a href="https://amdin.mega1000.pl/chat/{{ $token }}">Zmień daty dostawy</a>

<br>
<br>

Z pozdrowieniami, <br>
Zespół EPH Polska
