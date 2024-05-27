@php
    $token = \App\Entities\ChatAuctionFirm::where('chat_auction_id', $chatAuctionOffer?->chatAuction?->id)
        ->where('firm_id', $chatAuctionFirm?->firm?->id)
        ->first()
        ->token
@endphp


Informujemy że w ofercie numer {{ $chatAuctionOffer?->chatAuction?->chat?->order?->id }}  zostało dokonane przebicie najniższej ceny.
<br>

<br>
Prosimy wejść na poniższy link aby zobaczyć na ten moment zaproponowaną najniższą ceną.
<br>
<br>
<a href="https://admin.mega1000.pl/auctions/offer/create/{{ $token }}">
    Link do oferty
</a>
<br>
<br>
W razie problemów prosimy o wklejenie następującego linku w okno przeglądarki: https://admin.mega1000.pl/auctions/offer/create/{{ $token }}
<br>
<br>
Z pozdrowieniami
EPH POLSKA
