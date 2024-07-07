@php
    $firm = \App\Entities\Firm::where('symbol', 'IZOTERM')->first();

    $auctions = \App\Entities\ChatAuction::whereHas('firms', function ($query) use ($firm) {
            $query->where('firm_id', $firm->id);
        })
        ->with(['offers', 'offers.firm','chat.order.dates', 'chat.order.customer.addresses', 'chat.order.items.product.packing'])
        ->orderBy('updated_at', 'desc')
        ->paginate(20)
        ->toArray();

    $zipCodes = [];
    $auctions['data'] = array_filter($auctions['data'], function ($auction) use (&$zipCodes) {
        $address = $auction['chat']['order']['customer']['addresses'][0];
        $zipCodes[] = $address['zip_code'];
        return $address['latitude'] && $address['longitude'];
    });
@endphp

<div id="map" style="height: 100vh;"></div>

<script>
    var auctions = @json($auctions['data']);
    var zipCodes = @json($zipCodes);
    var firm = @json($firm);

    var map = L.map('map').setView([52.22977, 21.01178], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
    }).addTo(map);

    var markers = [];
    auctions.forEach(function (auction) {
        var address = auction.chat.order.customer.addresses[0];
        var marker = L.marker([address.latitude, address.longitude]).addTo(map);
        marker.bindPopup(`
            <b>${firm.name}</b><br>
            ${address.street} ${address.building_number}<br>
            ${address.zip_code} ${address.city}<br>
            <a href="/chat/${auction.chat.id}">Zobacz aukcję</a>
        `);
        markers.push(marker);
    });

    var group = new L.featureGroup(markers);
    map.fitBounds(group.getBounds());
</script>
