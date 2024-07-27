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
            <h3 class="font-semibold">Dane Klienta</h3>
            <p>ID Klienta: {{ $order->customer_id }}</p>
            <p>ID Magazynu: {{ $order->warehouse_id }}</p>
            <p>ID Pracownika: {{ $order->employee_id }}</p>
        </div>
    </div>

    <h3 class="font-semibold mb-2">Produkty w Zamówieniu</h3>
    <table class="w-full border-collapse border border-gray-300">
        <thead>
        <tr class="bg-gray-100">
            <th class="border p-2">ID Produktu</th>
            <th class="border p-2">Ilość</th>
            <th class="border p-2">Cena</th>
            <th class="border p-2">Suma</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($order->items as $item)
            <tr>
                <td class="border p-2">{{ $item->product_id }}</td>
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
