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
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
    <style>
        #map { height: 400px; width: 400px; }
    </style>
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
            <div class="col-md-6">
                <div id="map"></div>
            </div>
        </div>
        <div>
            <h3 class="font-semibold">Dane Dostawy</h3>{!! implode('<br>', $order->getInvoiceAddress()->only([
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
])) !!}
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
