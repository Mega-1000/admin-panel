@php
    $firm = \App\Entities\Firm::where('symbol', 'IZOTERM')->first();

    $auctions = \App\Entities\ChatAuction::whereHas('firms', function ($query) use ($firm) {
            $query->where('firm_id', $firm->id);
        })
        ->with(['offers', 'offers.firm','chat.order.dates', 'chat.order.addresses', 'chat.order.items.product.packing'])
        ->orderBy('updated_at', 'desc')
        ->paginate(50);
    $zipCodes = [];
    foreach ($auctions->items() as $auction) {
        $customer = $auction->chat?->order?->customer;
        $address = $customer?->addresses?->first();
        if ($address?->postal_code) {
            $zipCodes[] = $address->postal_code;
        }
    }
@endphp

<div id="map" style="height: 100vh;"></div>


<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-providers@1.13.0/leaflet-providers.js"></script>

<script>
    var map = L.map('map').setView([52.2297, 21.0122], 6); // Set initial view to Poland

    L.tileLayer.provider('OpenStreetMap.Mapnik').addTo(map);

    var zipCodes = @json($zipCodes);

    function addMarkers(zipCodes) {
        zipCodes.forEach(function(zipCode) {
            fetch(`https://nominatim.openstreetmap.org/search?postalcode=${zipCode}&country=Poland&format=json`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        var lat = parseFloat(data[0].lat);
                        var lon = parseFloat(data[0].lon);
                        L.marker([lat, lon]).addTo(map)
                            .bindPopup(`Zip Code: ${zipCode}`);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    }

    addMarkers(zipCodes);
</script>
