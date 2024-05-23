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

   <script>
       (() => {
           function sortTable(n) {
               let table = document.querySelector("table");
               let switching = true;
               let dir = "asc";
               let switchcount = 0;
               while (switching) {
                   switching = false;
                   let rows = Array.from(table.rows);
                   for (let i = 1; i < (rows.length - 1); i++) {
                       let shouldSwitch = false;
                       let x = rows[i].getElementsByTagName("TD")[n].innerText.toLowerCase().trim();
                       let y = rows[i + 1].getElementsByTagName("TD")[n].innerText.toLowerCase().trim();
                       shouldSwitch = shouldSwitchRows(x, y, dir);
                       if (shouldSwitch) {
                           [rows[i], rows[i + 1]] = [rows[i + 1], rows[i]];
                           switching = true;
                           switchcount++;
                       } else if (switchcount === 0 && dir === "asc") {
                           dir = "desc";
                           switching = true;
                       }
                   }
                   table.innerHTML = '';
                   rows.forEach(row => table.appendChild(row));
               }

               // Add the sorting class to the current column header
               let ths = table.getElementsByTagName("th");
               for (let i = 0; i < ths.length; i++) {
                   ths[i].classList.remove("asc", "desc");
               }
               ths[n].classList.add(dir);
           }

           function shouldSwitchRows(x, y, dir) {
               if (dir === "asc") {
                   return x > y;
               } else if (dir === "desc") {
                   return x < y;
               }
               return false;
           }

           // Add click event listeners to the sort buttons
           window.onload = () => {
               let sortButtons = document.querySelectorAll("th button.btn-primary");
               sortButtons.forEach((button, index) => {
                   button.addEventListener("click", () => sortTable(index));
               });
           };
       })();
   </script>

</head>

<body>
<table>
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
        <th>Końcowy koszt zamówienia</th>
    </tr>
    </thead>
    <tbody>
    @php
        $displayedFirmSymbols = [];
    @endphp

    @foreach($firms as $firm)
        @if(isset($auction) && $auction->offers->where('firm_id', $firm->firm->id)->count() === 0 || in_array($firm?->firm?->symbol ?? $firm?->symbol ?? [], $displayedFirmSymbols) || !isset($auction))
            @continue
        @endif

        <tr>
            <td>
                {{ $firm?->firm?->symbol ?? $firm->symbol ?? '' }}
                <br>
                Odległość: {{ round($firm->distance) }} KM
            </td>
            @php
                $displayedFirmSymbols[] =  $firm?->firm?->symbol ?? $firm->symbol ?? '';
            @endphp

            @foreach($products as $product)
                <td>
                    @php
                        $allProductsToBeDisplayed = \App\Entities\Product::where('product_name_supplier', $firm->firm->symbol)->where('product_group', $product->product->product_group)->get();

                        $offers = [];
                        foreach ($allProductsToBeDisplayed as $product) {
                            if ($auction->offers->where('firm_id', $firm->firm->id)->where('product_id', $product->id)->first())
                            {
                                $offers[] = $auction->offers->where('firm_id', $firm->firm->id)->where('product_id', $product->id)->first();
                            }
                        }

                        $offers = collect($offers)->sortBy('basic_price_net');
                    @endphp

                    @if($offers !== [])
                        @foreach($offers as $offer)
                            {{ \App\Entities\Product::find($offer->product_id)->additional_info1 }}: {{ round($offer->basic_price_net * 1.23, 2) }}
                            <br>
                        @endforeach
                        <span style="color: green">
                                - cena specjalnie dla ciebie
                            </span>
                    @else
                        No offer
                    @endif
                </td>
            @endforeach
            <td>
                @php
                    $totalCosts = [];
                    foreach ($products as $product) {
                        $allProductsToBeDisplayed = \App\Entities\Product::where('product_name_supplier', $firm->firm->symbol)->where('product_group', $product->product->product_group)->get();

                        $offers = [];
                        foreach ($allProductsToBeDisplayed as $product) {
                            if ($auction->offers->where('firm_id', $firm->firm->id)->where('product_id', $product->id)->first())
                            {
                                $offers[] = $auction->offers->where('firm_id', $firm->firm->id)->where('product_id', $product->id)->first();
                            }
                        }

                        $productPrices = $offers->pluck('basic_price_net')->sort()->values();
                        $combinations = generateCombinations($productPrices->toArray());

                        foreach ($combinations as $combination) {
                            $totalCost = array_sum($combination) * 1.23;
                            $totalCosts[] = round($totalCost / 3.33, 2);
                        }
                    }
                @endphp

                @foreach(array_unique($totalCosts) as $totalCost)
                    {{ $totalCost }} PLN<br>
                @endforeach
            </td>
        </tr>
    @endforeach

    @foreach($firms as $firm)
        @if(in_array($firm?->firm?->symbol ?? $firm?->symbol ?? [], $displayedFirmSymbols) || !isset($auction))
            @continue
        @endif

        {{-- ... Rest of the code ... --}}
    @endforeach
    </tbody>
</table>

@php
    function generateCombinations(array $arrays)
    {
        $result = [];
        $arrays = array_filter($arrays);
        if (empty($arrays)) {
            return $result;
        }

        $firstArray = array_shift($arrays);
        if (count($arrays) === 0) {
            foreach ($firstArray as $value) {
                $result[] = [$value];
            }
            return $result;
        }

        foreach ($firstArray as $value) {
            $childCombinations = generateCombinations($arrays);
            foreach ($childCombinations as $childCombination) {
                array_unshift($childCombination, $value);
                $result[] = $childCombination;
            }
        }

        return $result;
    }
@endphp

<div class="mt-4">
    <h4>Możliwe warianty końcowej ceny zamówienia:</h4>
    <ul>
        @foreach($totalOrderCosts as $cost)
            <li>{{ round($cost / 3.33, 2) }} PLN</li>
        @endforeach
    </ul>
</div>

<button class="btn btn-primary mt-2 mb-5" id="submit-button">
    Wyślij zamówienie
</button>
