<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.chat_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />
    <style>
        .info {
            padding: 6px 8px;
            font: 14px/16px Arial, Helvetica, sans-serif;
            background: white;
            background: rgba(255,255,255,0.8);
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            border-radius: 5px;
        }
        .info h4 {
            margin: 0 0 5px;
            color: #777;
        }
        .product {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
        }
        .product img {
            max-width: 150px;
        }
        .product-details {
            flex: 1;
        }
        .product-description {
            margin-bottom: 10px;
        }
        .pricing-table {
            margin-top: 10px;
        }
        #map {
            height: 400px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<div class="container my-4">
    <h3 class="text-center mb-4">
        Prosimy o skorygowanie ceny za m3 i zatwierdzenie poprzez użycie przycisku "Zapisz wszystkie ceny".
    </h3>
    <h4 class="text-center mb-4">
        !! Prosimy o wypełnienie formularza przetargowego, nawet jeśli Państwa oferta nie będzie najniższa, ponieważ klient może kierować się również jakością. !!
    </h4>
    <div class="row mb-4">
        <div class="col-md-6">
            <p class="text-center" style="font-size: 1.4em">
                <a target="__blank" href="https://admin.mega1000.pl/auctions/{{ $chat_auction_firm->chatAuction->id }}/end?isFirm=true&firmId={{ $chat_auction_firm->firm->id }}">Kliknij aby zobaczyć tabelę cen innych producentów</a>
                <br>
                Miejscowość dostawy: {{ $chat_auction_firm->chatAuction->chat->order->addresses->first()->city }}<br>
                Kod pocztowy: {{ $chat_auction_firm->chatAuction->chat->order->addresses->first()->postal_code }}<br>
                Numer oferty: {{ $chat_auction_firm->chatAuction->chat->order->id }}<br>
                Podział procentowy cena/jakość: <br>
                Cena: {{ $chat_auction_firm->chatAuction->price }} <br>
                Jakość: {{ $chat_auction_firm->chatAuction->quality }} <br>
                Data zakończenia przetargu: {{ $chat_auction_firm->chatAuction->end_of_auction }}<br>
                Uwagi klienta do tego zamówienia: {{ $chat_auction_firm->chatAuction->notes ?? 'brak' }}
            </p>
        </div>
        <div class="col-md-6">
            <div id="map"></div>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
    @endif

    @php
        $parentProductsDisplayed = [];
    @endphp

    @foreach($products as $p)
        {!! $p->count() > 1 && !in_array($p->first()->parentProduct?->id, $parentProductsDisplayed) ? '<div style="border: 4px red solid; border-radius: 10px"> <div style="font-weight: bolder; font-size: 24px"> Tylko jednen produkt z zaznaczonych na czerono zostanie wybrany przez klienta</div>' : '' !!}
            <div style="padding: 15px">
                @foreach($p as $product)
                    @php
                        if (in_array($product->parentProduct?->id, $parentProductsDisplayed)) {
                            continue;
                        } else {
                            $alreadyDisplayed = false;
                        }

                        $parentProductsDisplayed[] = $product->parentProduct?->id;
                    @endphp

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div style="{{ $alreadyDisplayed ? 'display: none' : '' }}">
                        @if(is_a($product, \App\Entities\Product::class))
                            <div class="alert alert-success text-center mb-4">
                                <h4>Najniższa cena na ten moment:
                                    @php
                                        $chatAuctionMinPrice = $chat_auction_firm->chatAuction
                                            ->offers()
                                            ->whereHas('product', function ($q) use ($product) {
                                                $q->where('product_group', $product->product_group);
                                                $q->where('additional_info1', $product->additional_info1);
                                            })
                                            ->min('basic_price_net');

                                        $orderItemMinPrices = \App\Entities\ProductPrice::whereHas('product', function ($q) use ($product) {
                                            $q->where('product_group', $product->product_group);
                                            $q->where('additional_info1', $product->additional_info1);
                                            })
                                            ->pluck('net_purchase_price_basic_unit')
                                            ->reject(function ($price) {
                                                return $price == 0; // Remove zero prices
                                            })
                                            ->sort()
                                            ->values(); // Sort prices and re-index array

                                        $secondMinPrice = $orderItemMinPrices->count() > 1 ? $orderItemMinPrices[1] : INF;

                                        $minPrice = min($chatAuctionMinPrice ?? INF, $secondMinPrice);


                                    @endphp

                                    {{ round($minPrice, 2) }} PLN

                                </h4>
                            </div>
                            <div class="product">
                                <img class="img-fluid" src="{{$product->url_for_website}}" onerror="this.onerror=null;this.src='http://via.placeholder.com/300'">
                                <div class="product-details">
                                    <div class="product-description">
                                        <h5>
                                            @php
                                                $name = $product->name;
                                                $words = explode(' ', $name);
                                                array_shift($words);
                                                $name = implode(' ', $words);
                                            @endphp
                                            {{ $name }}
                                        </h5>
                                        <p>Ilość m3: {{ round($product->quantity * $product->packing->numbers_of_basic_commercial_units_in_pack, 2) }}</p>
                                        <p>Cena podstawowa netto za m3: {{ round($product->price->net_purchase_price_basic_unit, 2) }} PLN</p>
                                    </div>
                                    <div>
                                        @php
                                            $productPrice = \App\Entities\ChatAuctionOffer::where('product_id', $product->id)
                                                    ->where('firm_id', $chat_auction_firm->firm_id);
                                            $productPrices = [
                                                'commercial_price_net' => $productPrice->min('commercial_price_net'),
                                                'basic_price_net' => $productPrice->min('basic_price_net'),
                                                'calculated_price_net' => $productPrice->min('calculated_price_net'),
                                                'aggregate_price_net' => $productPrice->min('aggregate_price_net'),
                                                'commercial_price_gross' => $productPrice->min('commercial_price_gross'),
                                                'basic_price_gross' => $productPrice->min('basic_price_gross'),
                                                'calculated_price_gross' => $productPrice->min('calculated_price_gross'),
                                                'aggregate_price_gross' => $productPrice->min('aggregate_price_gross'),
                                            ];
                                        @endphp

                                        @csrf
                                        <input type="hidden" class="unit_consumption" value="{{ $product->packing->unit_consumption }}">
                                        <input type="hidden" class="number_of_sale_units_in_the_pack" value="{{ $product->packing->number_of_sale_units_in_the_pack }}">
                                        <input type="hidden" class="numbers_of_basic_commercial_units_in_pack" value="{{ $product->packing->numbers_of_basic_commercial_units_in_pack }}">
                                        <input type="hidden" name="order_item_id" value="{{ $product->id }}">

                                        @include('chat/pricing_table', ['isAuctionOfferCreation' => true])
                                    </div>
                                    @php
                                        $product->current_firm_offers = $product
                                            ->chatAuctionOffers
                                            ->where('firm_id', $chat_auction_firm->firm->id)
                                            ->sortByDesc('id')
                                            ->first();
                                    @endphp
                                    <div class="form-check mt-3">
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" id="{{ $product->id }}" type="checkbox" form="main" name="send_notification.{{ $product->id }}" value="true"
                                                {{ $product->current_firm_offers?->send_notification ? 'checked' : '' }} {{ empty($product->current_firm_offers) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $product->id }}">Powiadamiaj mnie w przypadku przebicia najniższej ceny</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            @if( $product !== null )
                                <div class="product">
                                    <img class="img-fluid" width="100" height="100" src="{{$product->getImageUrl()}}" onerror="this.onerror=null;this.src='http://via.placeholder.com/300'">
                                    <div class="product-details">
                                        <h5>{{ $product->name }}</h5>
                                        <p>Cena: {{ $product->price->gross_selling_price_commercial_unit }} PLN / {{ $product->packing->unit_commercial }}</p>
                                    </div>
                                </div>
                            @endif
                        @endif
                {!! $p->count() > 1 ? '</div>' : '' !!}
            @endforeach
            </div>
        </div>
    @endforeach

    @if ($chat_auction_firm->firm->products->where('date_of_price_change', '<', now())->count() > 0)
        <div style="color: red; font-weight: bold; text-align: center; margin: 20px 0;">
            !! UWAGA !! Zauważyliśmy, że cenniki firmy którą reprezentujesz w naszym systemie mogą być nieaktualne. Prosimy o zaaktualizowanie ich lub zmianę daty ponownego powiadomienia w panelu pod linkiem poniżej:
            <br>
            <a class="btn btn-success" target="_blank" href="https://new.mega1000.pl/magazyn/aktualizacja-cen/{{ $chat_auction_firm->firm->id }}/zaktualizuj?isByFirm=true">Aktualizuj cenniki firmy reprezentowanej przez siebie</a>
        </div>
    @endif

    <form action="{{ route('auctions.offer.store', ['token' => $chat_auction_firm->token]) }}" method="POST" id="main" class="text-center mb-5">
        @csrf
        <button class="btn btn-primary mt-3"
            {{ $chat_auction_firm->firm->products->where('date_of_price_change', '<', now())->count() > 0 ? 'disabled' : '' }}
        >
            {{ $chat_auction_firm->firm->products->where('date_of_price_change', '<', now())->count() > 0 ? 'Przed zapisaniem cen prosimy o wypełnienie cen podstawowych i odświerzenie strony' : 'Zapisz wszystkie ceny' }}
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
<script src="{{ asset('js/vue-chunk.js') }}"></script>
<script src="{{ asset('js/vue-scripts.js') }}"></script>
<script src="{{ asset('js/libs/blink-title.js') }}"></script>
<script src="{{ asset('js/helpers/dynamic-calculator.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-polylinedecorator/1.6.0/leaflet.polylineDecorator.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(request()->query('success'))
        Swal.fire({
            title: "Pomyślnie zapisano ceny!",
            text: "Dziękujemy za udział w przetargu!",
            icon: "success"
        });
    @endif

    var map = L.map('map').setView([51.919438, 19.145136], 6);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Origin zip code
    var originZipCode = "{{ \App\Helpers\LocationHelper::nearestWarehouse($chat_auction_firm->chatAuction->chat->order->customer, $chat_auction_firm->firm)->zip_code }}";

    // Destination zip code (from the existing data)
    var destZipCode = "{{ $chat_auction_firm->chatAuction->chat->order->addresses->first()->postal_code }}";

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
        var url = `http://router.project-osrm.org/route/v1/driving/${origin[1]},${origin[0]};${destination[1]},${destination[0]}?overview=full&geometries=geojson`;

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
                    .bindPopup('Start: ' + originZipCode)
                    .openPopup();
                L.marker(destination).addTo(map)
                    .bindPopup('Destination: ' + destZipCode)
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
