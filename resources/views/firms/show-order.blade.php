<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold mb-2">Order #{{ $order['id'] }}</h1>
        <span class="px-2 py-1 rounded-full text-sm font-semibold
            @if($order['status_id'] == 5)
                bg-green-500 text-white
            @else
                bg-gray-500 text-white
            @endif
        ">
            Status: {{ $order['status_id'] }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
            $metrics = [
                ['title' => 'Total Price', 'value' => number_format($order['total_price'], 2), 'icon' => 'dollar-sign'],
                ['title' => 'Weight', 'value' => $order['weight'] . ' kg', 'icon' => 'package'],
                ['title' => 'Shipment Price', 'value' => number_format($order['shipment_price_for_client'], 2), 'icon' => 'truck'],
                ['title' => 'Order Date', 'value' => date('M d, Y', strtotime($order['created_at'])), 'icon' => 'calendar'],
            ];
        @endphp

        @foreach($metrics as $metric)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-500">{{ $metric['title'] }}</h3>
                    <span class="text-gray-400">
                        @include('components.icons.' . $metric['icon'])
                    </span>
                </div>
                <p class="text-2xl font-semibold">{{ $metric['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">Price Comparison</h2>
        <div id="price-comparison-chart" style="height: 400px;"></div>
    </div>

    <div>
        <h2 class="text-2xl font-semibold mb-4">Product Details</h2>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Purchase Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Selling Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Selling Price</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach(json_decode($order['products'], true) as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product['product_id'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product['quantity'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($product['net_purchase_price_basic_unit'], 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($product['net_selling_price_basic_unit'], 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($product['gross_selling_price_basic_unit'], 2) }}</td>
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
        var products = @json(json_decode($order['products'], true));
        var chartData = products.map(function(product) {
            return {
                x: 'Product ' + product.product_id,
                purchase: parseFloat(product.net_purchase_price_basic_unit),
                selling: parseFloat(product.net_selling_price_basic_unit),
                gross: parseFloat(product.gross_selling_price_basic_unit)
            };
        });

        var options = {
            series: [{
                name: 'Net Purchase Price',
                data: chartData.map(item => item.purchase)
            }, {
                name: 'Net Selling Price',
                data: chartData.map(item => item.selling)
            }, {
                name: 'Gross Selling Price',
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
                    text: 'Price'
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
