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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 40%;
            margin: auto;
        }

        @media screen and (max-width: 768px) {
            .container {
                width: 100%;
            }
        }

        table {
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        td {
            vertical-align: middle;
        }

        #chat-container {
            margin-top: 50px;
        }
    </style>
</head>

<body>
@if($firms->count() == 0)
    <div class="text-center">
        <h1>Tu za nie długo zaczną wyświetlać się wyniki twojego przetargu.</h1>
    </div>
@else
    <table style="width: fit-content; margin: 0 auto">
        <thead>
        <tr>
            <th>
                <h5 style="text-align: right">
                    Ceny brutto za m3
                </h5>
            </th>
            @php $iteration = 2; @endphp
            @foreach($products as $product)
                <th>
                    @php
                        $name = $product->product->name;
                        $words = explode(' ', $name);
                        array_shift($words);
                        $name = implode(' ', $words);
                    @endphp
                    {{ $name }}
                </th>
                @php $iteration++; @endphp
            @endforeach
            <th>Wartość w przypadku wybrania najtańszych opcji</th>
        </tr>
        </thead>
        <tbody>
        @php
            $displayedFirmSymbols = [];
            $firmCounter = 1;
        @endphp

        @foreach($firms as $firm)
            @if(isset($auction) && $auction->offers->where('firm_id', $firm->firm->id)->count() === 0 || in_array($firm?->firm?->symbol ?? $firm?->symbol ?? [], $displayedFirmSymbols) || !isset($auction))
                @continue
            @endif

            <tr>
                <td>
                    @if($firm->firm->id == request()->query('firmId'))
                        firma
                        <span style="color: red; font-weight: bold">
                            {{ $firm->firm->name }}
                        </span>
                    @else
                        {{ $firmCounter }}
                    @endif
                </td>
                @php
                    $displayedFirmSymbols[] =  $firm?->firm?->symbol ?? $firm->symbol ?? ''; // Add the symbol to the tracked array
                    $firmCounter++;
                @endphp

                @php
                    $totalCost = 0;
                @endphp

                @foreach($products as $product)
                    <td>
                        @php
                            $allProductsToBeDisplayed = \App\Entities\Product::where('product_name_supplier', $firm->firm->symbol)
                                ->where('product_group', $product->product->product_group)
                                ->get();

                            $offers = [];
                            foreach ($allProductsToBeDisplayed as $product) {
                                if ($auction->offers->where('firm_id', $firm->firm->id)->where('product_id', $product->id)->first()) {
                                    $offers[] = $auction->offers->where('firm_id', $firm->firm->id)->where('product_id', $product->id)->first();
                                }
                            }

                            usort($offers, function($a, $b) {
                                return $a->basic_price_net * 1.23 <=> $b->basic_price_net * 1.23;
                            });

                            $prices = [];
                            foreach ($offers as $offer) {
                                $prices[] = round($offer->basic_price_net * 1.23, 2);
                            }

                            $mediumPrice = count($prices) > 0 ? array_sum($prices) / count($prices) : 0;
                        @endphp

                        @if(!empty($offers))
                            <span style="color: green">
                                - specjalnie dla ciebie
                            </span>
                            <br>
                            {{ $mediumPrice }}

                            @php
                                $totalCost += $mediumPrice *
                                \App\Entities\OrderItem::where('order_id', $auction->chat->order->id)
                                    ->whereHas('product', function ($q) use ($product) {
                                        $q->where('product_group', $product->product_group);
                                    })->first()?->quantity;
                            @endphp
                        @else
                            No offer
                        @endif
                    </td>
                @endforeach
                <td>{{ round($totalCost / 3.33, 2) }}</td>
            </tr>
        @endforeach

        @foreach($firms as $firm)
            @if(in_array($firm?->firm?->symbol ?? $firm?->symbol ?? [], $displayedFirmSymbols) || !isset($auction))
                @continue
            @endif

            @php
                $symbol = $firm?->firm?->symbol ?? $firm->symbol ?? ''; // Assuming $firm->firm->symbol gives you the symbol you want to display
            @endphp
            @if((isset($auction) && $auction?->offers->where('firm_id', $firm?->firm?->id ?? $firm->id ?? '')->count() ?? 1 === 0 && !in_array($symbol, $displayedFirmSymbols)) || (!in_array($symbol, $displayedFirmSymbols) && true))
                <tr>
                    <td>
                        firma
                        @if($firm->firm->id == request()->query('firmId'))
                            <span style="color: red; font-weight: bold">
                                {{ $firm->firm->name }}
                            </span>
                        @else
                            {{ $firmCounter }}
                        @endif
                    </td>

                    @php
                        $prices = [];
                        $items = isset($auction) ? $auction?->chat?->order?->items : $order?->items;
                        $totalCost = 0;

                        foreach ($items as $item) {
                            $variations = App\Entities\Product::where('product_group', $item->product->product_group)
                                ->where('product_name_supplier', $symbol)
                                ->get();

                            $variations = $variations->sortBy(function($product) {
                                return $product->price->gross_purchase_price_basic_unit_after_discounts;
                            });

                            $prices[] = $variations->map(function($product) {
                                return $product->price->gross_purchase_price_basic_unit_after_discounts;
                            });

                            $totalCost += $variations->min('price.gross_purchase_price_basic_unit_after_discounts') * $item->quantity;
                        }
                    @endphp

                    @foreach($prices as $price)
                        <td>
                            @php
                                $mediumPrice = count($price->toArray()) > 0 ? array_sum($price->toArray()) / count($price->toArray()) : 0;
                            @endphp
                            {{ $mediumPrice }}
                        </td>
                    @endforeach
                    <td>{{ round($totalCost / 3.33, 2) }}</td>
                </tr>
                @php
                    $displayedFirmSymbols[] = $symbol; // Add the symbol to the array so it won't be displayed again
                    $firmCounter++;
                @endphp
            @endif
        @endforeach

        </tbody>
    </table>
@endif
