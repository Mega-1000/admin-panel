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
<div>
   @if(session()->get('success'))
       <div class="alert alert-success">
           Pomyślnie stworzono zamówienie i dodano przedstawicieli do chatu
       </div>
   @endif

   <div class="container" id="flex-container">
       <div id="chat-container">
           <div class="alert-success alert">
               Poleć naszą platformę znajomym, a my zaoferujemy Ci 30zł zniżki za każdego nowego użytkownika!
               <br>
               Po więcej informacji kliknij przycisk zobacz więcej
               <br>
               <br>
               <a href="https://mega1000.pl/polec-znajomego" target="_blank" class="btn btn-primary">
                   Zobacz więcej na temat promocji
               </a>
           </div>

           <div class="alert alert-primary mt-4">
               Jeśli jesteś zadowolony z ceny konkretnego producenta zaznacz checkboxy przy cenach dla każdego produktu po czym naciśnij przycisk "Wyślij zamówienie"
           </div>

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
                       </td> <!-- Display the firm symbol -->
                       @php
                           $displayedFirmSymbols[] =  $firm?->firm?->symbol ?? $firm->symbol ?? ''; // Add the symbol to the tracked array
                       @endphp

                       @php
                           $totalCost = 0;
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
                                        {
                                    }
                               @endphp

                               @if($offers !== [])
{{--                                   {{ $auction->offers()->where('firm_id', $firm->firm->id)->where('order_item_id', $product->id)->orderBy('created_at', 'asc')->first()->basic_price_gross }}--}}
                                    {{ dd($offers) }}

                                   <input type="checkbox" class="offer-checkbox" id="offer-checkbox{{ $offer->id }}" data-product-id="{{ $product->id }}" data-variation-id="{{ $offer->id }}">

                                   <span style="color: green">
                                       - cena specjalnie dla ciebie
                                   </span>

                                   @php
                                       $totalCost += $auction->offers()->where('firm_id',$firm->firm->id)->where('order_item_id', $product->id)->orderBy('created_at', 'asc')->first()->basic_price_gross * $product->quantity;
                                   @endphp
                               @else
                                   No offer
                               @endif
                           </td>
                       @endforeach
                       <td>{{ round($totalCost / 3.33, 2) }}</td>
                   </tr>
                   @foreach($products as $product)
                       <td>
                           @php
                               // Assuming `$product` comes with preloaded 'offers' relationship
                               $relevantOffers = $product->offers->filter(function($offer) use ($firm) {
                                   return $offer->firm_id === $firm->id;
                               });

                               // Just take the first relevant offer
                               $offer = $relevantOffers->first();
                           @endphp

                           @if($offer)
                               {{-- Display the offer's price --}}
                               {{ number_format($offer->basic_price_gross, 2) }} zł

                               <input type="checkbox" class="offer-checkbox" id="offer-checkbox{{ $offer->id }}" data-product-id="{{ $product->id }}" data-variation-id="{{ $offer->id }}">

                               <span style="color: green">
                - cena specjalnie dla ciebie
            </span>

                               @php
                                   $totalCost += $offer->basic_price_gross * $product->quantity;
                               @endphp
                           @else
                               No offer
                           @endif
                       </td>
                   @endforeach

               </tbody>
           </table>
           @endforeach

           <button class="btn btn-primary mt-2 mb-5" id="submit-button">
               Wyślij zamówienie
           </button>
       </div>

   </div>
</div>
</body>
</html>
