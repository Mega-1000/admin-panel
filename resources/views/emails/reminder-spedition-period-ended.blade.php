Szanowni Państwo,
<br>
<p>
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
</p>
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

    if (!$closestEmployee) {
        $closestEmployee = $company->employees->first();
    }

    App\Services\MessageService::createNewCustomerOrEmployee($order->chat, new Illuminate\Http\Request(['type' => 'Employee']), $closestEmployee);


    $token = app(\App\Helpers\MessagesHelper::class)->getChatToken(
        $order->id,
        $closestEmployee->id,
        'e',
    );
@endphp
<a href="https://admin.mega1000.pl/chat/{{ $token }}">Zmień daty dostawy</a>

<br>
<br>

Z pozdrowieniami, <br>
Zespół EPH Polska
