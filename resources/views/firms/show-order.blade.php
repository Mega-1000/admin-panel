@php
    $statusColors = [
        5 => 'bg-green-500',
        'default' => 'bg-gray-500'
    ];

    $metrics = [
        ['title' => 'Cena całkowita', 'value' => number_format($order->getTotalPrice(), 2) . ' zł', 'icon' => 'cash'],
        ['title' => 'Waga', 'value' => $order->getWeight() . ' kg', 'icon' => 'scale'],
        ['title' => 'Koszt wysyłki', 'value' => number_format($order->getShipmentPrice(), 2) . ' zł', 'icon' => 'truck'],
        ['title' => 'Data zamówienia', 'value' => $order->getCreatedAt()->format('d.m.Y'), 'icon' => 'calendar'],
    ];
@endphp

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold mb-2">Zamówienie #{{ $order->getId() }}</h1>
        <span class="px-2 py-1 rounded-full text-sm font-semibold text-white
            {{ $statusColors[$order->getStatusId()] ?? $statusColors['default'] }}">
            Status: {{ $order->getStatusId() }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach($metrics as $metric)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-500">{{ $metric['title'] }}</h3>
                    <span class="text-gray-400">
                        <x-dynamic-component :component="'icons.' . $metric['icon']" class="w-5 h-5" />
                    </span>
                </div>
                <p class="text-2xl font-semibold">{{ $metric['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">Porównanie cen</h2>
        <div id="price-comparison-chart" style="height: 400px;"></div>
    </div>

    <div>
        <h2 class="text-2xl font-semibold mb-4">Szczegóły produktów</h2>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID produktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ilość</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena zakupu netto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena sprzedaży netto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena sprzedaży brutto</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($order->getProducts() as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->getProductId() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->getQuantity() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($product->getNetPurchasePrice(), 2) }} zł</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($product->getNetSellingPrice(), 2) }} zł</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($product->getGrossSellingPrice(), 2) }} zł</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var products = @json($order->getProducts());
        var chartData = products.map(function(product) {
            return {
                x: 'Produkt ' + product.getProductId(),
                purchase: product.getNetPurchasePrice(),
                selling: product.getNetSellingPrice(),
                gross: product.getGrossSellingPrice()
            };
        });

        var options = {
            series: [{
                name: 'Cena zakupu netto',
                data: chartData.map(item => item.purchase)
            }, {
                name: 'Cena sprzedaży netto',
                data: chartData.map(item => item.selling)
            }, {
                name: 'Cena sprzedaży brutto',
                data: chartData.map(item => item.gross)
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: false,
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                },
            },
            xaxis: {
                categories: chartData.map(item => item.x),
            },
            yaxis: {
                title: {
                    text: 'Cena (zł)'
                },
            },
            legend: {
                position: 'right',
                offsetY: 40
            },
            fill: {
                opacity: 1
            }
        };

        var chart = new ApexCharts(document.querySelector("#price-comparison-chart"), options);
        chart.render();
    });
</script>
