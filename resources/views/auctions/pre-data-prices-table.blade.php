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
<table>
    <thead>
    <tr>
        <th>
            <h5 style="text-align: right">
                Ceny za m3
            </h5>
        </th>
        @php
            $items = isset($order) ? $order->items->pluck('product') : $products;
        @endphp

        @foreach($items as $product)
            <th>
                @php
                    $name = $product->name;
                    $words = explode(' ', $name);
                    array_shift($words);
                    $name = implode(' ', $words);
                @endphp
                {{ $name }}
            </th>
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
                {{ $symbol }}
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
                    {{ $price }} zł
                </td>
            @endforeach
        </tr>
        @php
            $displayedFirmSymbols[] = $symbol; // Add the symbol to the array so it won't be displayed again
        @endphp
    @endforeach

    </tbody>
</table>
