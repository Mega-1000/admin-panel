<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.chat_name') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
          integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css"
          integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
    <link href="{{ asset('css/views/chat/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ URL::asset('js/helpers/helpers.js') }}"></script>

    <style>
        .product {
            width: fit-content;
            margin: 20px auto 0;
        }
    </style>
</head>

<body>
    <div class="text-center mt-2">
        <h3>
            Prosimy o skorygowanie ceny za m3 i zatwierdzenie poprzez użycie przycisku "Aktualizuj" przy każdym produkcie oddzielnie.
            <br>
            <br>
            Miejscowość dostawy: {{ $chat_auction_firm->chatAuction->chat->order->addresses->first()->city }}
            <br>
            Kod pocztowy: {{ $chat_auction_firm->chatAuction->chat->order->addresses->first()->postal_code }}
            <br>
            Numer oferty: {{ $chat_auction_firm->chatAuction->chat->order->id }}
            <br>
            Uwagi klienta do tego zamówienia: {{ $chat_auction_firm->chatAuction->notes ?? 'brak' }}
        </h3>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
    @endif

    @foreach($products as $product)
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(is_a($product, \App\Entities\OrderItem::class))
            <div class="alert alert-success text-center"  style="width: 50%; margin: 0 auto; padding: 10px; margin-top: 30px">
                <h4>
                     Najniższa cena na ten moment:
                    {{ $chat_auction_firm->chatAuction->offers->where('order_item_id', $product->id)->min('basic_price_net') }} PLN
                </h4>
            </div>
            <div class="product">
                <img class="image-product" src="{{$product->product->getImageUrl()}}"
                     onerror="this.onerror=null;this.src='http://via.placeholder.com/300'"/>
                <div class="product-description">
                        <h4>
                        Najniższa cena na ten moment:
                        {{ $product->chatAuctionOffers->min('price') }} PLN
                    </h4>
                    <p>
                        @php
                            $name = $product->product->name;
                            $words = explode(' ', $name);
                            array_shift($words);
                            $name = implode(' ', $words);
                        @endphp
                        {{ $name }}
                    </p>
                    <p>
                        symbol: {{ $product->product->symbol }}
                    </p>
                    <p>
                        ilość: {{ $product->quantity }}
                    </p>
                    <p>
                        Wartość brutto: {{ $product->price }} PLN
                    </p>
                </div>
                <form style="display: flex; flex-direction: column" action="{{ route('auctions.offer.store', ['token' => $chat_auction_firm->token]) }}" method="POST">
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
                    <input type="hidden" class="unit_consumption"
                           value="{{ $product->product->packing->unit_consumption }}">
                    <input type="hidden" class="number_of_sale_units_in_the_pack"
                           value="{{ $product->product->packing->number_of_sale_units_in_the_pack }}">
                    <input type="hidden" class="numbers_of_basic_commercial_units_in_pack"
                           value="{{ $product->product->packing->numbers_of_basic_commercial_units_in_pack }}">
                    <input type="hidden" name="order_item_id" value="{{ $product->id }}">
                    @include('chat/pricing_table', ['isAuctionOfferCreation' => true])

                    @php
                        $product->current_firm_offers = $product->chatAuctionOffers->where('firm_id', $chat_auction_firm->firm->id)->sortByDesc('id')->first();
                    @endphp

                    <div class="d-flex" style="font-weight: bold; font-size: large">
                        Powiadamiaj mnie w przypadku przebicia najniższej ceny:
                        <input type="checkbox" name="send_notification" value="true" {{
                             $product->current_firm_offers ? ($product->current_firm_offers?->send_notification ? 'checked' : '') : ''
                        }}>
                    </div>

                    <button type="submit">Aktualizuj proponowane przez twoją firmę ceny</button>
                </form>
            </div>
        @else
            @if( $product !== null )
                <div class="product">
                    <img width="100" height="100" src="{{ $product->url_for_website }}"
                        />
                    {{ $product->name }}
                    cena: {{ $product->price->gross_selling_price_commercial_unit }} PLN / {{ $product->packing->unit_commercial }}
                </div>
            @endif
        @endif
    @endforeach

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
            integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous">
    </script>
    <script src="/js/jquery-ui.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"
            integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous">
    </script>
    <script src="{{ asset('js/vue-chunk.js') }}"></script>
    <script src="{{ asset('js/vue-scripts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/libs/blink-title.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/helpers/dynamic-calculator.js') }}"></script>
    <script>
        setTimeout(() => {
            const priceInputs = document.getElementsByName('basic_price_net');
            priceInputs.forEach((priceInput) => {
                onPriceChange(priceInput)
            });
        }, 1000);
    </script>
</body>
