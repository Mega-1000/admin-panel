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

            @if($firms->count() == 0)
                <div class="text-center">
                    <h1>Tu za nie długo zaczną wyświetlać się wyniki twojego przetargu.</h1>
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>
                                <h5 style="text-align: right">Ceny brutto za m3</h5>
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
                            <th>Wartość oferty w przypadku wybrania najtańszych opcji</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sortedFirms = collect();
                            $firmsWithMissingData = collect();
                            $displayedFirmSymbols = [];
                        @endphp

                        @foreach($firms as $firm)
                            @if(isset($auction) && $auction->offers->where('firm_id', $firm->firm->id)->count() === 0 || in_array($firm?->firm?->symbol ?? $firm?->symbol ?? [], $displayedFirmSymbols) || !isset($auction))
                                @continue
                            @endif

                            @php
                                $displayedFirmSymbols[] = $firm?->firm?->symbol ?? $firm->symbol ?? '';
                                $totalCost = 0;
                                $missingData = false;
                            @endphp

                            @foreach($products as $product)
                                @php
                                    $allProductsToBeDisplayed = \App\Entities\Product::where('product_name_supplier', $firm->firm->symbol)
                                        ->where('product_group', $product->product->product_group)
                                        ->get();

                                    $offers = [];
                                    foreach ($allProductsToBeDisplayed as $product) {
                                        if ($auction->offers->where('firm_id', $firm->firm->id)->where('product_id', $product->id)->first()) {
                                            $offers[] = \App\Entities\ChatAuctionOffer::whereHas('product', function ($q) use ($product) {$q->where('parent_id', $product->parent_id);})
                                                ->where('chat_auction_id', $auction->id)
                                                ->orderBy('basic_price_net', 'asc')
                                                ->first();
                                        }
                                    }

                                    usort($offers, function($a, $b) {
                                        return $a->basic_price_net <=> $b->basic_price_net;
                                    });

                                    $minOffer = collect($offers)->min('basic_price_net');

                                    if ($minOffer === 0 || empty($offers)) {
                                        $missingData = true;
                                        break;
                                    }

                                    $totalCost += round(($minOffer * 1.23), 2) *
                                        \App\Entities\OrderItem::where('order_id', $auction->chat->order->id)
                                            ->whereHas('product', function ($q) use ($product) {
                                                $q->where('product_group', $product->product_group);
                                            })->first()?->quantity;
                                @endphp
                            @endforeach

                            @if($missingData)
                                @php
                                    $firmsWithMissingData->push([
                                        'firm' => $firm,
                                        'totalCost' => round($totalCost / 3.33, 2)
                                    ]);
                                @endphp
                            @else
                                @php
                                    $sortedFirms->push([
                                        'firm' => $firm,
                                        'totalCost' => round($totalCost / 3.33, 2)
                                    ]);
                                @endphp
                            @endif
                        @endforeach

                        @foreach($sortedFirms->sortBy('totalCost') as $sortedFirm)
                            <tr>
                                <td>
                                    {{ $sortedFirm['firm']?->firm?->symbol ?? $sortedFirm['firm']->symbol ?? '' }}
                                    <br>
                                    Odległość: {{ round($sortedFirm['firm']->distance) }} KM
                                    <br>
                                    @php
                                        $employee = \App\Entities\Employee::where('email', $sortedFirm['firm']->email_of_employee)->first();
                                    @endphp
                                    @if($employee && $employee->phone)
                                        tel przedstawiciela: <br> +48 {{ $employee->phone }}
                                    @endif
                                </td>

                                @php
                                    $totalCost = 0;
                                @endphp

                                @foreach($products as $product)
                                    <td>
                                        @php
                                            $allProductsToBeDisplayed = \App\Entities\Product::where('product_name_supplier', $sortedFirm['firm']->firm->symbol)
                                                ->where('product_group', $product->product->product_group)
                                                ->get();

                                            $offers = [];
                                            foreach ($allProductsToBeDisplayed as $product) {
                                                if ($auction->offers->where('firm_id', $sortedFirm['firm']->firm->id)->where('product_id', $product->id)->first()) {
                                                    $offers[] = \App\Entities\ChatAuctionOffer::whereHas('product', function ($q) use ($product) {$q->where('parent_id', $product->parent_id);})
                                                        ->where('chat_auction_id', $auction->id)
                                                        ->orderBy('basic_price_net', 'asc')
                                                        ->where('firm_id', $sortedFirm['firm']->firm->id)
                                                        ->first();
                                                }
                                            }

                                            usort($offers, function($a, $b) {
                                                return $a->basic_price_net <=> $b->basic_price_net;
                                            });

                                            $minOffer = collect($offers)->min('basic_price_net');
                                            $minOfferPrice = $minOffer ? round($minOffer * 1.23, 2) : null;
                                            $minPurchasePrice = $allProductsToBeDisplayed->min('price.gross_purchase_price_basic_unit_after_discounts');

                                            $totalCost += ($minOfferPrice ?? $minPurchasePrice) *
                                                \App\Entities\OrderItem::where('order_id', $auction->chat->order->id)
                                                    ->whereHas('product', function ($q) use ($product) {
                                                        $q->where('product_group', $product->product_group);
                                                    })->first()?->quantity;
                                        @endphp

                                        @if(!empty($offers))
                                            @foreach($offers as $offer)
                                                {{ \App\Entities\Product::find($offer->product_id)->additional_info1 }}:
                                                {{ round($offer->basic_price_net * 1.23, 2) }}
                                                @if(auth()->id())
                                                    ({{ $offer->basic_price_net }})
                                                @endif
                                                <br>
                                            @endforeach

                                            <span style="color: green">- specjalnie dla ciebie</span>
                                        @else
                                            No offer
                                        @endif
                                    </td>
                                @endforeach

                                <td>
                                    {{ round($totalCost / 3.33, 2) }}
                                    <br>
                                    <a class="btn btn-primary" href="https://admin.mega1000.pl/make-order/{{ $sortedFirm['firm']?->firm?->symbol }}/{{ $order->id }}">
                                        Wyślij zamówienie na tego producenta
                                    </a>

                                    @if(auth()->id())
                                        <a class="btn btn-secondary" href="https://admin.mega1000.pl/auctions/offer/create/{{ $sortedFirm['firm']->token }}">
                                            Dodaj cenę jako ta firma
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @foreach($firmsWithMissingData->sortBy('totalCost') as $firm)
                            <tr>
                                <td>
                                    {{ $firm['firm']?->firm?->symbol ?? $firm['firm']->symbol ?? '' }}
                                    <br>
                                    Odległość: {{ round($firm['firm']->distance) }} KM
                                    <br>
                                    @php
                                        $employee = \App\Entities\Employee::where('email', $firm['firm']->email_of_employee)->first();
                                    @endphp
                                    @if($employee && $employee->phone)
                                        tel przedstawiciela: <br> +48 {{ $employee->phone }}
                                    @endif
                                </td>

                                @foreach($products as $product)
                                    <td>
                                    {{ \App\Entities\Product::where('product_name_supplier', $firm['firm']->firm->symbol)->where('product_group', $product->product->product_group)->get() }}
                                        @php
                                            $allProductsToBeDisplayed = \App\Entities\Product::where('product_name_supplier', $firm['firm']->firm->symbol)
                                                ->where('product_group', $product->product->product_group)
                                                ->get();

                                            $offers = [];
                                            foreach ($allProductsToBeDisplayed as $product) {
                                                if ($auction->offers->where('firm_id', $firm['firm']->firm->id)->where('product_id', $product->id)->first()) {
                                                    $offers[] = \App\Entities\ChatAuctionOffer::whereHas('product', function ($q) use ($product) {$q->where('parent_id', $product->parent_id);})
                                                        ->where('chat_auction_id', $auction->id)
                                                        ->orderBy('basic_price_net', 'asc')
                                                        ->where('firm_id', $firm['firm']->firm->id)
                                                        ->first();
                                                }
                                            }

                                            usort($offers, function($a, $b) {
                                                return $a->basic_price_net <=> $b->basic_price_net;
                                            });

                                            $minOffer = collect($offers)->min('basic_price_net');
                                            $minOfferPrice = $minOffer ? round($minOffer * 1.23, 2) : null;
                                            $minPurchasePrice = $allProductsToBeDisplayed->min('price.gross_purchase_price_basic_unit_after_discounts');
                                        @endphp

                                        @if(!empty($offers))
                                            @foreach($offers as $offer)
                                                {{ \App\Entities\Product::find($offer->product_id)->additional_info1 }}:
                                                {{ round($offer->basic_price_net * 1.23, 2) }}
                                                @if(auth()->id())
                                                    ({{ $offer->basic_price_net }})
                                                @endif
                                                <br>
                                            @endforeach

                                            <span style="color: green">- specjalnie dla ciebie</span>
                                        @else
                                            No offer
                                        @endif
                                    </td>
                                @endforeach

                                <td>
                                    {{ round($firm['totalCost'] / 3.33, 2) }}
                                    <br>
                                    <a class="btn btn-primary" href="https://admin.mega1000.pl/make-order/{{ $firm['firm']?->firm?->symbol }}/{{ $order->id }}">
                                        Wyślij zamówienie na tego producenta
                                    </a>

                                    @if(auth()->id())
                                        <a class="btn btn-secondary" href="https://admin.mega1000.pl/auctions/offer/create/{{ $firm['firm']->token }}">
                                            Dodaj cenę jako ta firma
                                        </a>
                                    @endif
                                </td>
                            </tr>
@endforeach

@foreach($firms as $firm)
    @if(in_array($firm?->firm?->symbol ?? $firm?->symbol ?? [], $displayedFirmSymbols) || !isset($auction))
        @continue
    @endif

    @php
        $symbol = $firm?->firm?->symbol ?? $firm->symbol ?? '';
        $coordinatesOfUser = \DB::table('postal_code_lat_lon')->where('postal_code', $order->getDeliveryAddress()->postal_code)->get()->first();

        if ($coordinatesOfUser) {
            $raw = \DB::selectOne(
                'SELECT w.id, pc.latitude, pc.longitude, 1.609344 * SQRT(
                    POW(69.1 * (pc.latitude - :latitude), 2) +
                    POW(69.1 * (:longitude - pc.longitude) * COS(pc.latitude / 57.3), 2)) AS distance
                FROM postal_code_lat_lon pc
                JOIN warehouse_addresses wa on pc.postal_code = wa.postal_code
                JOIN warehouses w on wa.warehouse_id = w.id
                WHERE w.firm_id = :firmId AND w.status = \'ACTIVE\'
                ORDER BY distance
                limit 1',
                [
                    'latitude' => $coordinatesOfUser->latitude,
                    'longitude' => $coordinatesOfUser->longitude,
                    'firmId' => $firm->firm->id
                ]
            );

            $distance = round($raw?->distance, 2);
        }
    @endphp

    @if((isset($auction) && $auction?->offers->where('firm_id', $firm?->firm?->id ?? $firm->id ?? '')->count() ?? 1 === 0 && !in_array($symbol, $displayedFirmSymbols)) || (!in_array($symbol, $displayedFirmSymbols) && true))
        <tr>
            <td>
                {{ $symbol }}
                <br>
                Odległość: {{ $distance ?? 'N/A' }} KM
                <br>
                @php
                    $employee = \App\Entities\Employee::where('email', $firm->email_of_employee)->first();
                @endphp
                @if($employee && $employee->phone)
                    tel przedstawiciela: <br> +48 {{ $employee->phone }}
                @endif
            </td>

            @php
                $prices = [];
                $items = isset($auction) ? $auction?->chat?->order?->items : $order?->items;
                $totalCost = 0;
                $missingData = false;

                foreach ($items as $item) {
                    $variations = App\Entities\Product::where('product_group', $item->product->product_group)
                        ->where('product_name_supplier', $symbol)
                        ->get();

                    $variations = $variations->sortBy(function($product) {
                        return $product->price->gross_purchase_price_basic_unit_after_discounts;
                    });

                    if ($variations->isEmpty() || $variations->min('price.net_special_price_basic_unit') === 0) {
                        $missingData = true;
                        break;
                    }

                    $prices[] = $variations;

                    $validPrices = $variations->filter(function($variation) {
                        return !($variation->price->net_special_price_basic_unit == 0 || empty($variation->price->net_special_price_basic_unit));
                    });

                    $minPrice = $validPrices->min('price.net_special_price_basic_unit');

                    // Update the total cost with the minimum valid price times the item quantity
                    $totalCost += $minPrice * $item->quantity;

                }
            @endphp

            @if($missingData)
                @foreach($products as $product)
                    <td>No offer</td>
                @endforeach
            @else
                @foreach($prices as $price)
                    <td>
                        @foreach($price as $p)
                            {{ $p->price->product->additional_info1 }}:
                            {{ $p?->price->gross_purchase_price_basic_unit_after_discounts }}
                            @if(auth()->id())
                                ({{ round($p?->price->net_purchase_price_basic_unit_after_discounts, 2) }})
                            @endif
                            <br>
                        @endforeach
                    </td>
                @endforeach
            @endif

            <td>
                @if($missingData)
                    Missing data
                @else
                    {{ round($totalCost / 3.33, 2) }}
                    <br>
                    <a class="btn btn-primary" href="https://admin.mega1000.pl/make-order/{{ $symbol }}/{{ $order->id }}">
                        Wyślij zamówienie na tego producenta
                    </a>

                    @if(auth()->id())
                        <a class="btn btn-secondary" href="https://admin.mega1000.pl/auctions/offer/create/{{ $firm->token }}">
                            Dodaj cenę jako ta firma
                        </a>
                    @endif
                @endif
            </td>
        </tr>
        @php
            $displayedFirmSymbols[] = $symbol;
        @endphp
    @endif
@endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
<script>
const table = document.querySelector('table');
const rows = Array.from(table.querySelectorAll('tbody tr'));

rows.sort((a, b) => {
const aTotalValue = parseFloat(a.querySelector('td:last-child').textContent.trim());
const bTotalValue = parseFloat(b.querySelector('td:last-child').textContent.trim());
return aTotalValue - bTotalValue;
});

const tableBody = table.querySelector('tbody');
tableBody.innerHTML = '';

rows.forEach(row => {
tableBody.appendChild(row);
});
</script>
</body>
