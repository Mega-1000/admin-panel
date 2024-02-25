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
                        // Assuming $product->name or similar property exists
                        // Split name to identify the prefix (e.g., "fasada") and the suffix (e.g., "045")
                        list($prefix, $suffix) = preg_split('/\s+/', "$product->name", 2) + [null, ''];
                        $groupedItems[$prefix][] = $suffix;
                    }
                @endphp

                @foreach($groupedItems as $prefix => $suffixes)
                    <th colspan="{{ count($suffixes) }}">
                        {{ $prefix }}
                    </th>
                @endforeach
                {{ dd($groupedItems) }}
            </tr>
            <tr>
                <th></th> <!-- Placeholder for the "Ceny za m3" column -->
                @foreach($groupedItems as $prefix => $suffixes)
                    @php
                        // Sort suffixes numerically
                        natsort($suffixes);
                    @endphp
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
    <script>
        // setTimeout(() => {
        //     // Function to sort table rows
        //     function sortTableByColumn(table, column, asc = true) {
        //         const dirModifier = asc ? 1 : -1;
        //         const tBody = table.tBodies[0];
        //         const rows = Array.from(tBody.querySelectorAll("tr"));
        //
        //         // Sort each row
        //         const sortedRows = rows.sort((a, b) => {
        //             const aColText = a.querySelector(`td:nth-child(${column})`).textContent.trim();
        //             const bColText = b.querySelector(`td:nth-child(${column})`).textContent.trim();
        //
        //             // Check for no offer and sort those to the bottom
        //             const aValueIsNoOffer = aColText === "Brak oferty";
        //             const bValueIsNoOffer = bColText === "Brak oferty";
        //
        //             if (aValueIsNoOffer && bValueIsNoOffer) return 0; // Both have no offer, keep order
        //             if (aValueIsNoOffer) return 1; // Only A has no offer, move A down
        //             if (bValueIsNoOffer) return -1; // Only B has no offer, move B down
        //
        //             // If neither row has "Brak oferty", proceed with standard comparison
        //             return aColText.localeCompare(bColText, undefined, {numeric: true, sensitivity: 'base'}) * dirModifier;
        //         });
        //
        //         // Remove all existing TRs from the table
        //         while (tBody.firstChild) {
        //             tBody.removeChild(tBody.firstChild);
        //         }
        //
        //         // Re-add the newly sorted rows
        //         tBody.append(...sortedRows);
        //
        //         // Update sort direction classes
        //         table.querySelectorAll("th").forEach(th => th.classList.remove("asc", "desc"));
        //         table.querySelector(`th:nth-child(${column})`).classList.toggle("asc", asc);
        //         table.querySelector(`th:nth-child(${column})`).classList.toggle("desc", !asc);
        //     }
        //
        //     // Add click event to all column headers
        //     document.querySelectorAll(".container th").forEach(headerCell => {
        //         headerCell.addEventListener("click", () => {
        //             const tableElement = headerCell.parentElement.parentElement.parentElement;
        //             const headerIndex = Array.prototype.indexOf.call(headerCell.parentNode.children, headerCell);
        //             const currentIsAscending = headerCell.classList.contains("asc");
        //
        //             sortTableByColumn(tableElement, headerIndex, !currentIsAscending);
        //         });
        //     });
        // }, 1000);
    </script>
</body>
