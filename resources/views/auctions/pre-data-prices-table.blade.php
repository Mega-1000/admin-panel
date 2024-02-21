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

        th.asc::after {
            content: " ↓"; /* Change to arrow SVG or symbol as needed */
        }

        th.desc::after {
            content: " ↑"; /* Change to arrow SVG or symbol as needed */
        }
    </style>
</head>

<body>
<div class="container">
    <table>
        <thead>
        <tr>
            <th>Ceny za m3</th>
            @php
                $items = isset($order) ? $order->items->pluck('product') : $products;
            @endphp

            @php
                $groupedItems = [];
                foreach ($items as $product) {
                    $product->name = \App\Helpers\AuctionsHelper::getTrimmedProductGroupName($product);
                    $product->name = str_split($product->name, 0)[1];
                    // Assuming $product->name or similar property exists
                    // Split name to identify the prefix (e.g., "fasada") and the suffix (e.g., "045")
                    list($prefix, $suffix) = preg_split('/\s+/', "$product->name", 2) + [null, ''];
                    $groupedItems[$prefix][] = $suffix;
                    dd($product->name);
                }
            @endphp

            @foreach($groupedItems as $prefix => $suffixes)
                <th colspan="{{ count($suffixes) }}">
                    {{ $prefix }}
                </th>
            @endforeach
        </tr>
        <tr>
            <th></th> <!-- Placeholder for the "Ceny za m3" column -->
            @foreach($groupedItems as $prefix => $suffixes)
                @foreach($suffixes as $suffix)
                    <th>{{ $suffix }}</th>
                @endforeach
            @endforeach
        </tr>
        </thead>
        <tbody>

        @php
            $displayedFirmSymbols = [];
        @endphp

        @foreach($firms as $firm)
            @php
                $symbol = $firm->symbol; // Assuming $firm->firm->symbol gives you the symbol you want to display
            @endphp

            <tr>
                <td>
                    <a href="https://mega1000.pl/{{ $symbol }}/{{ \App\Entities\Category::where('name', $symbol)->first()?->id }}/no-layout">
                        {{ $symbol }}
                    </a>
                </td>

                @php
                    $prices = [];
                    if (!is_array($items)) {
                        $items = $items->toArray();
                    }

                    foreach ($items as $item) {
                        $variation = App\Entities\Product::where('product_group', $item['product_group'])->where('product_name_supplier', $firm->symbol)->first();

                        $prices[] = $variation?->price->gross_purchase_price_basic_unit_after_discounts;
                    }
                @endphp

                @foreach($prices as $price)
                    <td>
                        @if($price)
                            {{ $price }} zł
                        @else
                            Brak oferty
                        @endif

                    </td>
                @endforeach
            </tr>
            @php
                $displayedFirmSymbols[] = $symbol; // Add the symbol to the array so it won't be displayed again
            @endphp
        @endforeach

        </tbody>
    </table>
</div>
</body>
