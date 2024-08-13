<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-polylinedecorator/1.6.0/leaflet.polylineDecorator.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <style>
        #map { height: 300px; width: 100%; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
<nav class="bg-indigo-600 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-white.svg" alt="Workflow">
                <div class="ml-10 flex items-baseline space-x-4">
                    <a href="#" class="bg-indigo-700 text-white px-3 py-2 rounded-md text-sm font-medium">Zamówienia</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
{{--            <h2 class="text-2xl font-bold text-gray-900">Zamówienie #{{ $order->id }}</h2>--}}
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Szczegóły i status zamówienia</p>
        </div>

        @php
            $sumOfPurchase = 0;
            $items = $order->items;

            foreach ($items as $item) {
                $pricePurchase = $item->net_purchase_price_commercial_unit_after_discounts ?? 0;
                $quantity = $item->quantity ?? 0;
                $sumOfPurchase += floatval($pricePurchase) * intval($quantity);
            }

            $totalItemsCost = $sumOfPurchase * 1.23;
            $transportCost = 0;
        @endphp
        @if (isset($order->invoices))
            @foreach ($order->invoices as $invoice)
                @if ($invoice->invoice_type === 'buy')
                    <a target="_blank" href="/storage/invoices/{{ $invoice->invoice_name }}" style="margin-top: 5px;">Faktura</a>

                    {{--            @if ($invoice['is_visible_for_client'])--}}
                    {{--                <p class="invoice__visible">Widoczna</p>--}}
                    {{--            @else--}}
                    {{--                <p class="invoice__invisible">Niewidoczna</p>--}}
                    {{--            @endif--}}

                    {{--            <a href="#" class="change__invoice--visibility" onclick="changeInvoiceVisibility({{ $invoice['id'] }})">Zmieńwidoczność</a>--}}

                    <a class="remove__invoices" href="/delete-invoice?id={{ $invoice->id }}">Usuń</a>
                    <hr>
                @endif
            @endforeach
            <br />
            @php
                if (preg_match('/taskOrder-(\d+)/', $order->id, $matches)) {
                      $id = $matches[1];
                  }
            @endphp
        @endif

        @if(\App\Entities\BuyingInvoice::where('order_id', $order->id)->first())
            <hr>
            Faktury zakupu:
            <br>
        @endif

        <a href="{{ rtrim(config('app.front_nuxt_url'), '/') }}/magazyn/awizacja/0/0/{{ $order->id }}/wyslij-fakture" target="_blank" class="text-white bg-blue-500 py-2 px-4 rounded font-medium">
            Dodaj Fakturę proformę
        </a>

        <br>

        @foreach(\App\Entities\BuyingInvoice::where('order_id', $order->id)->where('analized_by_claute', true)->get() as $invoice)
            Faktura numer: {{ $invoice->invoice_number }} Warość: {{ $invoice->value }} PLN
                <br>

                <a href="{{ $invoice->file_url }}">
                    Analiza AI
                </a>
            <a class="btn btn-danger" href="/admin/delete-buying-invoice/{{ $invoice->id }}">
                Usuń fakturę
            </a>
            <hr>
        @endforeach


        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Mapa Dostawy</h3>
            <div id="map" class="mt-4 rounded-lg shadow-md"></div>
        </div>

        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $order->status->name }}
                            </span>
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Data utworzenia</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->created_at->format('d.m.Y H:i') }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Data aktualizacji</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->updated_at->format('d.m.Y H:i') }}</dd>
                </div>
{{--                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">--}}
{{--                    <dt class="text-sm font-medium text-gray-500">Całkowita cena</dt>--}}
{{--                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">--}}
{{--                        <span class="font-semibold text-lg text-indigo-600">{{ number_format($o, 2, ',', ' ') }} zł</span>--}}
{{--                    </dd>--}}
{{--                </div>--}}
            </dl>
        </div>

        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Dane Dostawy</h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                @foreach($order->getInvoiceAddress()->only([
                    'firstname',
                    'lastname',
                    'firmname',
                    'nip',
                    'phone_code',
                    'phone',
                    'address',
                    'flat_number',
                    'city',
                    'postal_code',
                    'email'
                ]) as $key => $value)
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">{{ ucfirst($key) }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Produkty w Zamówieniu</h3>
        </div>
        <div class="px-4 sm:px-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa produktu</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ilość</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena netto za opakowanie</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wartość netto towaru</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($order->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($item->net_purchase_price_commercial_unit_after_discounts, 2, ',', ' ') }} zł</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($item->net_purchase_price_commercial_unit_after_discounts * $item->quantity, 2, ',', ' ') }} zł</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Dodatkowe Informacje</h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Koszt wysyłki</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ number_format($order->shipment_price_for_client, 2, ',', ' ') }} zł</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Płatność za pobraniem</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->cash_on_delivery_amount ? number_format($order->cash_on_delivery_amount, 2, ',', ' ') . ' zł' : 'Nie dotyczy' }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Wysyłka za granicę</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->shipping_abroad ? 'Tak' : 'Nie' }}</dd>
                </div>
            </dl>
        </div>

    </div>
</main>

<footer class="bg-gray-800 mt-12">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <p class="text-center text-gray-300 text-sm">
            © 2024 EPH Polska. Wszystkie prawa zastrzeżone.
        </p>
    </div>
</footer>

<script>
    var map = L.map('map').setView([51.919438, 19.145136], 6);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Origin zip code
    var originZipCode = "{{ \App\Helpers\LocationHelper::nearestWarehouse($order, $order->items->first()->product->firm)->address->postal_code }}";

    // Destination zip code (from the existing data)
    var destZipCode = "{{ $order->addresses->first()->postal_code }}";

    // Function to get coordinates from zip code
    function getCoordinates(zipCode) {
        return $.getJSON('https://nominatim.openstreetmap.org/search?format=json&postalcode=' + zipCode + '&country=pl')
            .then(function(data) {
                if (data.length > 0) {
                    return [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                } else {
                    throw new Error('Could not find coordinat for zip code: ' + zipCode);
                }
            });
    }

    // Function to get route from OSRM
    function getRoute(origin, destination) {
        var url = `https://router.project-osrm.org/route/v1/driving/${origin[1]},${origin[0]};${destination[1]},${destination[0]}?overview=full&geometries=geojson`;

        return $.getJSON(url);
    }

    // Main function to set up the map
    function setupMap() {
        console.log('Setting up map...');
        Promise.all([getCoordinates(originZipCode), getCoordinates(destZipCode)])
            .then(function([origin, destination]) {
                console.log('Magazyn fabryki', origin);
                console.log('Lokalizacja dostawy', destination);

                // Add markers
                L.marker(origin).addTo(map)
                    .bindPopup('Magazyn fabryki' + originZipCode)
                    .openPopup();
                L.marker(destination).addTo(map)
                    .bindPopup('Lokalizacja dostawy ' + destZipCode)
                    .openPopup();

                // Get and display the route
                return getRoute(origin, destination).then(function(data) {
                    var coordinates = data.routes[0].geometry.coordinates.map(function(coord) {
                        return [coord[1], coord[0]];
                    });

                    var polyline = L.polyline(coordinates, {color: 'blue'}).addTo(map);

                    // Fit the map to show the entire route
                    map.fitBounds(polyline.getBounds());

                    // Calculate distance and time
                    var distanceKm = (data.routes[0].distance / 1000).toFixed(2);
                    var estimatedTimeHours = (data.routes[0].duration / 3600).toFixed(2);

                    // Add info control
                    var info = L.control();
                    info.onAdd = function () {
                        this._div = L.DomUtil.create('div', 'info');
                        this.update(distanceKm, estimatedTimeHours);
                        return this._div;
                    };
                    info.update = function (distance, time) {
                        this._div.innerHTML = '<h4>Przewidywana dostawa</h4>' +
                            'Dystans: ' + distance + ' km<br>' +
                            'Czas: ' + time + ' godzin';
                    };
                    info.addTo(map);

                    console.log('Map setup complete');
                });
            })
            .catch(function(error) {
                console.error('Error setting up map:', error);
                alert('Error setting up map: ' + error.message);
            });
    }

    // Call the setup function when the document is ready
    $(document).ready(function() {
        console.log('Document ready, calling setupMap');
        setupMap();

    });
</script>
</body>
</html>
