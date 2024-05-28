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
        @if($p->count() > 1)
            <div style="border: 4px red solid; border-radius: 10px; margin-top: 10px">
                <div style="font-weight: bolder; font-size: 24px">Tylko jednen produkt z zaznaczonych na czerono zostanie wybrany przez klienta</div>
                @endif
                <div style="padding: 15px">
                    @foreach($p as $product)
                        @php
                            if (in_array($product->parentProduct?->id, $parentProductsDisplayed)) {
                                $alreadyDisplayed = true;
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
                                <!-- Product code -->
                            @else
                                @if( $product !== null )
                                    <!-- Non-product code -->
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
                @if($p->count() > 1)
            </div>
        @endif
    @endforeach

    <form action="{{ route('auctions.offer.store', ['token' => $chat_auction_firm->token]) }}" method="POST" id="main" class="text-center mb-5">
        @csrf
        <button class="btn btn-primary mt-3">Zapisz wszystkie ceny</button>
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

    setTimeout(() => {
        const priceInputs = document.getElementsByName('basic_price_net');
        priceInputs.forEach((priceInput) => {
            onPriceChange(priceInput)
        });
    }, 1000);

    // Initialize Leaflet map
    var map = L.map('map').setView([51.919438, 19.145136], 5);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Get the zip code from the data
    var zipCode = "{{ $chat_auction_firm->chatAuction->chat->order->addresses->first()->postal_code }}";

    // Use an external API to get the latitude and longitude from the zip code
    $.getJSON('https://nominatim.openstreetmap.org/search?format=json&postalcode=' + zipCode + '&country=pl', function(data) {
        if (data.length > 0) {
            var lat = data[0].lat;
            var lon = data[0].lon;

            // Add a marker at the location
            L.marker([lat, lon]).addTo(map)
                .bindPopup('Przybliżona lokalizacja dostawy <br> Kod pocztowy: ' + zipCode)
                .openPopup();

            // Center the map on the location
            map.setView([lat, lon], 12);
        } else {
            console.log('Nie znaleziono kodu pocztowego');
        }
    });
</script>
</body>
</html>
