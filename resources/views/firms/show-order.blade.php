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

<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Zamówienie #{{ $order->id }}</h2>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
            <h3 class="font-semibold">Szczegóły Zamówienia</h3>
            <p>Status: {{ $order->status_id }}</p>
            <p>Data utworzenia: {{ $order->created_at->format('d.m.Y H:i') }}</p>
            <p>Data aktualizacji: {{ $order->updated_at->format('d.m.Y H:i') }}</p>
            <p>Całkowita cena: {{ number_format($order->total_price, 2, ',', ' ') }} zł</p>
        </div>
        <div>
            <h3 class="font-semibold">Dane Dostawy</h3>
            <p>{!! implode($order->addresses()->where('type', '=', 'DELIVERY_ADDRESS')->first()->toArray(), '<br>') !!}</p>
        </div>
    </div>

    <h3 class="font-semibold mb-2">Produkty w Zamówieniu</h3>
    <table class="w-full border-collapse border border-gray-300">
        <thead>
        <tr class="bg-gray-100">
            <th class="border p-2">Nazwa produktu</th>
            <th class="border p-2">Ilość</th>
            <th class="border p-2">Cena</th>
            <th class="border p-2">Suma</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($order->items as $item)
            <tr>
                <td class="border p-2">{{ $item->product->name }}</td>
                <td class="border p-2">{{ $item->quantity }}</td>
                <td class="border p-2">{{ number_format($item->price / $item->quantity, 2, ',', ' ') }} zł</td>
                <td class="border p-2">{{ number_format($item->price, 2, ',', ' ') }} zł</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="mt-6">
        <h3 class="font-semibold mb-2">Dodatkowe Informacje</h3>
        <p>Koszt wysyłki: {{ number_format($order->shipment_price_for_client, 2, ',', ' ') }} zł</p>
        <p>Płatność za pobraniem: {{ $order->cash_on_delivery_amount ? number_format($order->cash_on_delivery_amount, 2, ',', ' ') . ' zł' : 'Nie dotyczy' }}</p>
        <p>Proponowana płatność: {{ number_format($order->proposed_payment, 2, ',', ' ') }} zł</p>
        <p>Wysyłka za granicę: {{ $order->shipping_abroad ? 'Tak' : 'Nie' }}</p>
    </div>

</div>

<footer class="bg-white mt-12">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <p class="text-center text-gray-500 text-sm">
            © 2024 EPH Polska. Wszystkie prawa zastrzezone.
        </p>
    </div>
</footer>
</body>
</html>
