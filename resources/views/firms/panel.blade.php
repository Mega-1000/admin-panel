<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>
<body class="bg-gray-100 min-h-screen">
<nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg" alt="Workflow">
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="#" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Zamówienia
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Przetargi
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<main class="py-10">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Zamówienia</h1>
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($orders as $order)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                            <div class="p-4">
                                <h2 class="text-xl font-semibold text-gray-800 mb-2">Zamówienie #{{ $order->id }}</h2>
                                @if($order->labels->contains('id', 77))
                                    <div class="bg-red-100 border-l-4 border-yellow-500 text-yellow-700 p-3 mb-4">
                                        <p class="font-bold">
                                            NIE POTWIERDZONO AWIZACJI!
                                            <br>
                                            Otrzymujesz powiadomienia co 15 minut. kliknij

                                            <a
                                                href="https://new.mega1000.pl/magazyn/awizacja/34655/1431333/86398"
                                                target="__blank"
                                                class="btn btn-primary"
                                            >
                                                tutaj
                                            </a>

                                            aby potwierdzić awizację.
                                        </p>
                                    </div>
                                @endif

                                @if($order->labels->contains('id', 260))
                                    <div class="bg-green-100 border-l-4 border-yellow-500 text-yellow-700 p-3 mb-4">
                                        <p class="font-bold">
                                            PRZELEW ZOSTAŁ WYKONANY
                                        </p>
                                    </div>
                                @endif

                                @if($order->labels->contains('id', 261))
                                    <div class="bg-red-100 border-l-4 border-yellow-500 text-yellow-700 p-3 mb-4">
                                        <p class="font-bold">
                                            PRZELEW ZOSTAŁ WYKONANY - prosimy o potwierdzenie otrzymujesz powiadomienia co 15 minut. kliknij
                                            <br>
                                            <a href="{{ route('store-payment-confirmation-confirm', $order->id) }}" class="btn btn-primary">TUTAJ</a>
                                        </p>
                                    </div>
                                @endif

                                @if($order->labels->contains('id', 243))
                                    <div class="bg-red-100 border-l-4 border-yellow-500 text-yellow-700 p-3 mb-4">
                                        <p class="font-bold">
                                            DATY SPEDYCJI ZOSTAŁY ZAKOŃCZONE - Prosimy o informacje czy zlecenie zostało wydane aktualnie otrzymujesz powiadomienia co 15 minut
                                            <br>
                                            Jeśli zamówienie juź wyjechało, prosimy o potwierdzenie tego faktu klikając w przycisk poniżej:
                                            <a href="{{ rtrim(config('app.front_nuxt_url'), '/') . "/magazyn/awizacja/{$order->orderWarehouseNotifications->first()->id}/{$order->warehouse_id}/{$order->id}/wyslij-fakture" }}">
                                                <button class="btn btn-success">Towar został wydany</button>
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
                                            <a href="https://admin.mega1000.pl/chat/{{ $token }}" class="btn btn-primary">Zmień daty dostawy</a>
                                        </p>
                                    </div>
                                @endif

                                @if($order->labels->contains('id', 270))
                                    <div class="bg-red-100 border-l-4 border-yellow-500 text-yellow-700 p-3 mb-4">
                                        <p class="font-bold">
                                            NIE OTRZYMALIŚMY INFORMACJI CZY ZAMÓWIENIE WYJEDZIE JUTRO - Prosimy o informacje czy zlecenie zostanie wydane jutro aktualnie otrzymujesz powiadomienia co 15 minut
                                            <br>
                                            Jeśli zamówienie zostanie wysłane jutro prosimy o wypełnienie formularza po kliknięciu: <a href="https://admin.mega1000.pl/orders/set-order-as-shipping-today/{{$order->id}}" class="btn btn-primary">Tutaj</a>
                                            <br>
                                            <br>
                                            Jeśli zamówienie nie zostanie wysłane jutro kliknij <a href="https://admin.mega1000.pl/orders/set-order-as-not-shipping-today/{{$order->id}}">Tutaj</a>
                                        </p>
                                    </div>
                                @endif

                            @if($order->labels->contains('id', 66))
                                    <div class="bg-green-100 border-l-4 border-yellow-500 text-yellow-700 p-3 mb-4">
                                        <p class="font-bold">
                                            Towar został wydany!
                                        </p>
                                    </div>
                                @endif

                                <div class="text-sm text-gray-600">
                                    <p><strong>Data stworzenia:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
                                    <p><strong>Status:</strong> {{ $order->status->name }}</p>
                                    <p><strong></strong></p>
                                    <p><strong>Wartość:</strong> {{ number_format($order->getValue(), 2) }}zł</p>
                                </div>
                                <div class="mt-4">
                                    <a href="/firm-panel-actions/order/{{ $order->id }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Szczegóły</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="bg-white mt-12">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <p class="text-center text-gray-500 text-sm">
            © 2024 EPH Polska. Wszystkie prawa zastrzezone.
        </p>
    </div>
</footer>
</body>
</html>
