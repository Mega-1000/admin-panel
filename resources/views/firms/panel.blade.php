<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

                                            <a href="
{{ rtrim(config('app.front_nuxt_url'), "/") . "/zamowienie/mozliwe-do-realizacji/brak-danych/{$this->order->id}" }}
                                            ">
                                                tutaj
                                            </a>

                                            aby potwierdzić awizację.
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
