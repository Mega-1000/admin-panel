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
            margin: auto;
            overflow-y: auto; /* Make sure the container allows for scrolling */
            max-height: 90vh; /* Adjust based on your needs */
        }

        @media screen and (max-width: 768px) {
            .container {
                width: 100%;
            }
        }

        table {
            border-collapse: collapse;
            width: 100%; /* Ensure table fills the container */
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #4CAF50;
            color: white;
            position: sticky;
            top: 0; /* Adjust if you have a specific offset */
            z-index: 1; /* Ensures the header is above other content */
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

        th {
            cursor: pointer;
        }

    </style>
</head>

<body>
    <div >
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
                        // Assuming $product->name or similar property exists
                        // Split name to identify the prefix (e.g., "fasada") and the suffix (e.g., "045")
                        list($prefix, $suffix) = preg_split('/\s+/', "$product->name", 2) + [null, ''];
                        $groupedItems[$prefix][] = $suffix;
                    }
                @endphp

                @foreach($groupedItems as $prefix => $suffixes)
                    <th  colspan="{{ count($suffixes) }}">
                        {{ $prefix }}
                    </th>
                @endforeach
            </tr>
            <tr>
                <th></th> <!-- Placeholder for the "Ceny za m3" column -->
                @foreach($groupedItems as $prefix => $suffixes)
                    @php
                        // Sort suffixes numerically
                        natsort($suffixes);
                    @endphp
                    @foreach($suffixes as $suffix)
                        <th title="Kliknij aby sortować">{{ $suffix }} <br> <br> Sortuj po cenie</th>
                    @endforeach
                @endforeach
            </tr>
            </thead>
            <tbody>

            @php
                $displayedFirmSymbols = [];
            @endphp

            @foreach($firms as $firm)
                <tr>
                    <td>
                        <a href="https://mega1000.pl/{{ $firm->symbol }}/{{ \App\Entities\Category::where('name', $firm->symbol)->first()?->id }}/no-layout">
                            {{ $firm->symbol }}
                        </a>
                    </td>

                    @php
                        // Initialize an array to store prices for each grouped item
                        $groupedPrices = [];


                        foreach ($groupedItems as $prefix => $suffixes) {
                            foreach ($suffixes as $suffix) {
                                // Construct the name pattern to match for this product
                                $namePattern = $prefix . ' ' . $suffix;

                                if(isset($order)) {
                                    $lastSpacePosition = strrpos($namePattern, ' ');

                                    $namePattern = substr($namePattern, 0, $lastSpacePosition);
                                }

                                // Fetch the variation based on the firm's symbol and the name pattern
                                $variation = App\Entities\Product::where('product_name_supplier', $firm->symbol)
                                    ->where('name', 'like', '%' . $namePattern . '%')
                                    ->first();

                                // Store the price in the groupedPrices array, using the prefix and suffix as keys
                                $groupedPrices[$prefix][$suffix] = $variation?->price->gross_purchase_price_basic_unit_after_discounts;
                            }
                        }
                    @endphp

                    @foreach($groupedItems as $prefix => $suffixes)
                        @php
                            natsort($suffixes)
                        @endphp

                        @foreach($suffixes as $suffix)
                            @php
                                // Retrieve the price from the groupedPrices array
                                $price = $groupedPrices[$prefix][$suffix] ?? null;
                            @endphp

                            <td>
                                @if($price)
                                    {{ $price }}
                                @else
                                    Brak
                                @endif
                            </td>
                        @endforeach
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function(){
            $('th').click(function(){
                var table = $(this).parents('table').eq(0);
                var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()));
                this.asc = !this.asc;
                if (!this.asc){
                    rows = rows.reverse();
                    $(this).html($(this).html().replace(' ↓', ' ↑'));
                } else {
                    $(this).html($(this).html().replace(' ↑', ' ↓'));
                }
                table.find('th').not(this).each(function(){
                    $(this).html($(this).html().replace(' ↓', '').replace(' ↑', ''));
                });
                for (var i = 0; i < rows.length; i++){table.append(rows[i]);}
            });
            function comparer(index) {
                return function(a, b) {
                    var valA = getCellValue(a, index), valB = getCellValue(b, index);
                    return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
                };
            }
            function getCellValue(row, index){ return $(row).children('td').eq(index).text(); }
        });
    </script>

</body>
