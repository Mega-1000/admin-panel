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
    <style>
        .product {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
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
    </style>
</head>
<body>
<div class="container my-4">
    <h3 class="text-center mb-4">
        Prosimy o skorygowanie ceny za m3 i zatwierdzenie poprzez użycie przycisku "Aktualizuj" przy każdym produkcie oddzielnie.
    </h3>
    <p class="text-center mb-4">
        Miejscowość dostawy: {{ $chat_auction_firm->chatAuction->chat->order->addresses->first()->city }}<br>
        Kod pocztowy: {{ $chat_auction_firm->chatAuction->chat->order->addresses->first()->postal_code }}<br>
        Numer oferty: {{ $chat_auction_firm->chatAuction->chat->order->id }}<br>
        Uwagi klienta do tego zamówienia: {{ $chat_auction_firm->chatAuction->notes ?? 'brak' }}
    </p>

    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
    @endif

    @php
        $parentProductsDisplayed = [];
    @endphp

    @foreach($products as $product)
        @foreach($product as $product)
        @php
//            if (in_array($product->parentProduct?->id, $parentProductsDisplayed)) {
//                $alreadyDisplayed = true;
//            } else {
//                $alreadyDisplayed = false;
//            }
//            $totalQuantity = $chat_auction_firm->chatAuction->chat->order
//                    ->items()->whereHas('product', function ($q) use ($product) { $q->where('parent_id', $product->parentProduct->id); })->get()->sum('quantity');
//
//            $parentProductsDisplayed[] = $product->parentProduct?->id;
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

    <div style="{{ false ? 'display: none' : '' }}">
        @if(is_a($product, \App\Entities\OrderItem::class))
            <div class="alert alert-success text-center mb-4">
                <h4>Najniższa cena na ten moment: {{ $chat_auction_firm->chatAuction->offers->where('order_item_id', $product->id)->min('basic_price_net') }} PLN</h4>
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
                        <p>Symbol: {{ $product->symbol }}</p>
                        <p>Ilość paczek: {{ $totalQuantity }}</p>
                        <p>Wartość brutto: {{ $product->price }} PLN</p>
                    </div>
                    <div>
                        @php
                            $productPrice = \App\Entities\ChatAuctionOffer::where('order_item_id', $product->id)
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
                        $product->current_firm_offers = $product->chatAuctionOffers->where('firm_id', $chat_auction_firm->firm->id)->sortByDesc('id')->first();
                    @endphp
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="send_notification.{{ $product->id }}" value="true"
                            {{ $product->current_firm_offers?->send_notification ? 'checked' : '' }} {{ empty($product->current_firm_offers) ? 'cheched' : '' }}>
                        <label class="form-check-label">Powiadamiaj mnie w przypadku przebicia najniższej ceny</label>
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
    </div>
        @endforeach
    @endforeach

    <form action="{{ route('auctions.offer.store', ['token' => $chat_auction_firm->token]) }}" method="POST" id="main" class="text-center">
        @csrf
        <button class="btn btn-primary">Zapisz wszystkie ceny</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
<script src="{{ asset('js/vue-chunk.js') }}"></script>
<script src="{{ asset('js/vue-scripts.js') }}"></script>
<script src="{{ asset('js/libs/blink-title.js') }}"></script>
<script src="{{ asset('js/helpers/dynamic-calculator.js') }}"></script>
<script>
    setTimeout(() => {
        const priceInputs = document.getElementsByName('basic_price_net');
        priceInputs.forEach((priceInput) => {
            onPriceChange(priceInput)
        });
    }, 1000);
</script>
</body>
</html>
