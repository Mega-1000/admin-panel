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

        /* Styles for the table header row */
        thead {
            background-color: #4CAF50;
            color: white;
            position: sticky;
            top: 0; /* Stick to the top of the container */
            z-index: 2; /* Ensure the header row appears above other rows */
        }

        /* Styles for the second row */
        tbody tr:nth-child(1) {
            position: sticky;
            top: 39px; /* Adjust to the height of your <th> elements */
            background-color: white;
            z-index: 1; /* Lower z-index than the header row */
        }

        /* Styles for the first column */
        tbody td:first-child {
            position: sticky;
            left: 0; /* Stick to the left side of the container */
            background-color: white;
            z-index: 1; /* Same z-index as the second row */
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2; /* Alternating row background color */
        }

        td {
            vertical-align: middle;
        }

        #chat-container {
            margin-top: 50px;
        }

        /* Styles for sorting arrows */
        th.asc::after {
            content: " ↓"; /* Change to arrow SVG or symbol as needed */
        }

        th.desc::after {
            content: " ↑"; /* Change to arrow SVG or symbol as needed */
        }

        th {
            cursor: pointer;
        }

        /* Style for sorted thead */
        thead.sorted {
            background-color: #4CAF50; /* Green color */
            color: white;
        }
    </style>
</head>

<body>
    <div style="margin-top: 10px">
        <table>
            <thead>
            <tr>
                <th>Ceny brutto za m3</th>
                @php
                    $items = isset($order) ? $order->items->pluck('product') : $products;
                @endphp

                @php
                    if (!isset($order)) {
                        $items = $items->sortBy('order')->reject(function ($item) {
                            return $item['order'] === null || $item['order'] === 0;
                        });
                    }

                    $groupedItems = [];
                    $eanMapping = []; // Assuming this array maps prefixes to their ean_of_collective_packing

                    foreach ($items as $product) {
                        $product->name = \App\Helpers\AuctionsHelper::getTrimmedProductGroupName($product);
                        list($prefix, $suffix) = preg_split('/\s+/', "$product->name", 2) + [null, ''];
                        $groupedItems[$prefix][] = $suffix;
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
                    @php

                    @endphp
                    @foreach($suffixes as $suffix)
                        <th>
                            {{ $suffix }}
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1 .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z"/>
                            </svg>
                        </th>
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
                        <a href="https://mega1000.pl/{{ $firm->symbol }}/{{ \App\Entities\Category::where('name', $firm->symbol)->first()?->id }}/">
                            {{ $firm->symbol }}
                            Odległość:
                            {{ round($firm?->distance) }}KM
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
                                    ->whereHas('children')
                                    ->first();

                                // Store the price in the groupedPrices array, using the prefix and suffix as keys
                                $groupedPrices[$prefix][$suffix] = [
                                    $variation?->price->gross_purchase_price_basic_unit_after_discounts,
                                    $variation?->id
                                ];
                            }
                        }
                    @endphp

                    @foreach($groupedItems as $prefix => $suffixes)
                        @foreach($suffixes as $suffix)
                            @php
                                // Retrieve the price from the groupedPrices array
                                $price = $groupedPrices[$prefix][$suffix][0] ?? null;
                                $id = $groupedPrices[$prefix][$suffix][1] ?? null;
                            @endphp

                            <td>
                                @php
                                    $product = App\Entities\Product::find($id);
                                @endphp

                                @if($price)
                                    @php($dateOfPriceChange = \Carbon\Carbon::create($product->date_of_price_change))
                                    <a href="https://mega1000.pl/singleProduct/{{ $id }}/"
                                       style="{{ $dateOfPriceChange->lessThan(\Carbon\Carbon::now()) ? 'color: red;' : '' }}">
                                        {{ $price }}
                                    </a>
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
        $(document).ready(function() {
            $('th').each(function() {
                // Initialize a sort state on each `th`
                $(this).data('sortState', '');
            });

            $('th').click(function() {
                var table = $(this).parents('table').eq(0);
                var columnIndex = $(this).index();
                var sortState = $(this).data('sortState');
                var rows = table.find('tbody tr').toArray().sort(comparer(columnIndex, sortState));

                // Remove the 'sorted' class from the thead
                table.find('thead').removeClass('sorted');

                // Toggle the sort state for this column
                if (sortState === 'asc') {
                    $(this).data('sortState', 'desc');
                    $(this).html($(this).html().replace(' ↓', ' ↑')); // Adjust if using different indicators
                } else {
                    $(this).data('sortState', 'asc');
                    $(this).html($(this).html().replace(' ↑', ' ↓')); // Adjust if using different indicators
                }

                // Add the 'sorted' class to the thead if this column is sorted
                if (sortState !== '') {
                    table.find('thead').addClass('sorted');
                }

                // Reset sort state indicators for other columns
                table.find('th').not(this).each(function() {
                    $(this).data('sortState', ''); // Clear the sort state
                    $(this).html($(this).html().replace(' ↓', '').replace(' ↑', '')); // Remove any sort indicators
                });

                // Re-append rows in sorted order
                for (var i = 0; i < rows.length; i++) {
                    table.append(rows[i]);
                }
            });

            function comparer(index, sortState) {
                return function(a, b) {
                    var valA = getCellValue(a, index), valB = getCellValue(b, index);
                    var result = $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
                    // Reverse the result if the current sort state is descending
                    return (sortState === 'desc') ? result * -1 : result;
                };
            }

            function getCellValue(row, index) {
                return $(row).children('td').eq(index).text();
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    parent.postMessage({ url: event.target.href }, '*');
                });
            });
        });
    </script>
</body>
