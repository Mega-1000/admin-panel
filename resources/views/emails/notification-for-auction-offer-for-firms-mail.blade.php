Informujemy że w ofercie numer {{ $chatAuctionOffer->chatAuction->chat->order->id }}  zostało dokonane przebicie najniższej ceny.
<br>
<br>
Prosimy wejść na poniższy link aby zobaczyć na ten moment zaproponowaną najniższą ceną.
<br>
<br>
<a href="{{ route('auctions.offer.create', ['token' => $chatAuctionOffer->auctionFirm->token]) }}">Link do oferty</a>
<br>
<br>
Z pozdrowieniami
EPH POLSKA
