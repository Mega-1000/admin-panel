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
        :root {
            --primary-color: #4A90E2;
            --secondary-color: #50E3C2;
            --background-color: #F8F9FA;
            --text-color: #333;
            --accent-color: #FF6B6B;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border: none;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
        }

        tr {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #3A7BD5;
        }

        .alert {
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: var(--secondary-color);
            color: #fff;
        }

        #chat-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 50px;
        }

        .header-button {
            margin-top: 10px;
        }

        .lowest-price-certificate {
            background-color: var(--accent-color);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-top: 30px;
        }

        .lowest-price-certificate h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .lowest-price-certificate p {
            font-size: 16px;
        }

        @media screen and (max-width: 768px) {
            .container {
                width: 95%;
                padding: 10px;
            }

            table {
                font-size: 14px;
            }

            th, td {
                padding: 10px;
            }
        }


        .price-guarantee {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 2rem;
            width: 300px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        .price-guarantee::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
        }
        .icon {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }
        h2 {
            color: #ffffff;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
    <script src="https://cdn.tailwindcss.com"></script>
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
            <div>
                Oglądasz tabele zapytania: {{ $order->id }}
                @if(auth()->user())
                    <br>
                    Użytkownik: {{ $order->customer->login }} Numer telefonu: {{ $order->addresses()->first()->phone }}
                    <a target="_blank" class="btn btn-primary header-button"
                       href="{{ route('orders.goToBasket', ['id' => $order->id]) }}"
                       for="add-item">
                        Edytuj zamówienie w koszyku
                    </a>
                    <br>
                    <br>
                @endif
            </div>

            <div class="price-guarantee mb-4" style="width: 100%;">
                <div style="display: flex; gap: 10px">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1ZM12 11.99H19C18.47 16.11 15.72 19.78 12 20.93V12H5V6.3L12 3.19V11.99Z" fill="#FFD700"/>
                    </svg>
                    <img src="https://mega1000.pl/logo.webp" alt="" style="width: 80px; height: 80px">
                </div>
                <h2 class="h2 text-white">Gwarancja najniższej ceny!</h2>
                <p class="text-lg text-white">Gwarantujemy państwu, że ceny otrzymane po zakończeniu przetargu będą najniższe na rynku niezależnie od producenta! Znalazłeś lepszą ofertę? Zadzwoń do nas a my obniżymy cenę o dodatkowe 100zł! +48 576 205 389</p>
            </div>

            <div class="price-guarantee mb-4" style="width: 100%;">
                <h3 class="h3 text-white">Opcje płatności</h3>
                <p class="text-lg text-white">
                    Dbamy o Państwa wygodę i bezpieczeństwo, dlatego oferujemy dwie opcje płatności:
                    <br>
                    1. Przedpłata: Zapłać z góry, a my natychmiast przystąpimy do realizacji zamówienia.
                    <br>
                    2. Płatność przy odbiorze: Po przyjeździe kuriera, prosimy o wykonanie szybkiego przelewu online. Gdy tylko otrzymamy potwierdzenie, kurier rozładuje towar (wymagane jest 10% przedpłaty)
                    <br>
                    Obie metody gwarantują bezpieczeństwo transakcji zarówno dla Państwa, jak i dla nas.
                </p>
            </div>

{{--            <div class="alert-success alert">--}}
{{--                Poleć naszą platformę znajomym, a my zaoferujemy Ci 30zł zniżki za każdego nowego użytkownika!--}}
{{--                <br>--}}
{{--                Wystarczy podać numer telefonu!--}}
{{--                <br>--}}
{{--                <br>--}}
{{--                <a href="https://mega1000.pl/polec-znajomego" target="_blank" class="btn btn-primary">--}}
{{--                    Zobacz więcej na temat promocji--}}
{{--                </a>--}}
{{--            </div>--}}

            implode(',', Employee::whereHas('firm', function ($q) { $q->whereHas('products', function ($q) { $q->where('variation_group', 'styropiany'); }); })->get()->pluck('phone')->toArray())

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
                                    $pcOffers = [];
                                    foreach ($allProductsToBeDisplayed as $product) {
                                        if ($auction->offers->where('firm_id', $firm->firm->id)->where('product_id', $product->id)->first()) {
                                            $offers[] = \App\Entities\ChatAuctionOffer::whereHas('product', function ($q) use ($product) {$q->where('parent_id', $product->parent_id);})
                                                ->where('chat_auction_id', $auction->id)
                                                ->orderBy('basic_price_net', 'asc')
                                                ->first();
                                        }

                                        $pcOffers[] = \App\Entities\ChatAuctionOffer::whereHas('product', function ($q) use ($product) {$q->where('parent_id', $product->parent_id);})
                                            ->where('chat_auction_id', $auction->id)
                                            ->orderBy('basic_price_net', 'asc')
                                            ->first();
                                    }

                                    usort($offers, function($a, $b) {
                                        return $a->basic_price_net <=> $b->basic_price_net;
                                    });

                                    $minOffer = collect($pcOffers)->min('basic_price_net');

                                    $totalCost += (round(($minOffer * 1.23), 2) *
                                        \App\Entities\OrderItem::where('order_id', $auction->chat->order->id)
                                            ->whereHas('product', function ($q) use ($product) {
                                                $q->where('product_group', $product->product_group);
                                            })->first()?->quantity) * $product?->packing?->numbers_of_basic_commercial_units_in_pack;
                                @endphp
                            @endforeach

                            @php
                                $sortedFirms->push([
                                    'firm' => $firm,
                                    'totalCost' => round($totalCost, 2)
                                ]);
                            @endphp
                        @endforeach

                        @foreach($sortedFirms->sortBy('totalCost') as $sortedFirm)
                            <tr>
                                <td>
                                    {{ $sortedFirm['firm']?->firm?->symbol ?? $sortedFirm['firm']->symbol ?? '' }}
                                    <br>
                                    Odległość: {{ round($sortedFirm['firm']->distance) }} KM
                                    <br>
                                    @php
                                        $employee = \App\Helpers\LocationHelper::getNearestEmployeeOfFirm($order->customer, $sortedFirm['firm']->firm);
                                    @endphp
                                    @if($employee && $employee->phone && auth()->id())
                                        tel przedstawiciela: <br> +48 {{ $employee->phone }} {{ $employee->firstname }}
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
                                                if ($auction->offers()->where('firm_id', $sortedFirm['firm']->firm->id)->whereHas('product', function ($q) use ($product) {$q->where('parent_id', $product->parent_id);})->first()) {
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
                                            $minPurchasePrice = $allProductsToBeDisplayed->min('price.net_selling_price_basic_unit') * 1.23;

                                            $orderItem = \App\Entities\OrderItem::where('order_id', $auction->chat->order->id)
                                            ->whereHas('product', function ($q) use ($product) {
                                                $q->where('product_group', $product->product_group);
                                            })->first();

                                            $totalCost += ($minOfferPrice * ($orderItem?->quantity ?? 0)) * $product?->packing?->numbers_of_basic_commercial_units_in_pack ?? 0.33333;
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
                                    {{ round($totalCost, 2) }}
                                    <br>
                                    <a class="btn btn-primary" href="https://admin.mega1000.pl/make-order/{{ $sortedFirm['firm']?->firm?->symbol }}/{{ $order->id }}">
                                        Wyślij zamówienie na tego producenta
                                    </a>

                                    @if(auth()->id())
                                        <button class="{{ App\Entities\ChatAuctionFirm::where('firm_id', App\Entities\Firm::where('symbol', $sortedFirm['firm']?->firm?->symbol)->first()->id)->where('chat_auction_id', $order->chat->auctions->first()->id)->first()?->token }} {{ $order->id }} btn btn-primary" id="sendSmsAboutAuction">
                                            Wyślij smsa do przedstawiciela w sprawie przetargu
                                        </button>

                                        <a class="btn btn-secondary" href="https://admin.mega1000.pl/auctions/offer/create/{{ $sortedFirm['firm']->token }}">
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
                @if($employee && $employee->phone && auth()->id())
                    tel przedstawiciela: <br> +48 {{ $employee->phone }} {{ $employee->firstname }}
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

                    $minPrice = $validPrices->min('price.net_selling_price_basic_unit') * 1.23;

                    if (empty($minPrice)) {
                        $totalCost += 100000000;
                    }
                    $totalCost += ($minPrice * $item->quantity) * $item->product->packing->numbers_of_basic_commercial_units_in_pack;
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
                            {{ round($p?->price->gross_selling_price_basic_unit, 2) }}
                            @if(auth()->id())
                                ({{ round($p?->price->gross_selling_price_basic_unit / 1.23, 2) }})
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
                    {{ round($totalCost, 2) }}
                    <br>
                    <a class="btn btn-primary" href="https://admin.mega1000.pl/make-order/{{ $symbol }}/{{ $order->id }}">
                        Wyślij zamówienie na tego producenta
                    </a>

                    @if(auth()->id())
                        <button class="{{ App\Entities\ChatAuctionFirm::where('firm_id', App\Entities\Firm::where('symbol', $symbol)->first()->id)->where('chat_auction_id', $order->chat->auctions->first()->id)->first()?->token }} {{ $order->id }} btn btn-primary" id="sendSmsAboutAuction">
                            Wyślij smsa do przedstawiciela w sprawie przetargu
                        </button>

                        <a class="btn btn-secondary" href="https://admin.mega1000.pl/auctions/offer/create/{{ App\Entities\ChatAuctionFirm::where('firm_id', App\Entities\Firm::where('symbol', $symbol)->first()->id)->where('chat_auction_id', $order->chat->auctions->first()->id)->first()?->token }}">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const table = document.querySelector('table');
const rows = Array.from(table.querySelectorAll('tbody tr'));

rows.sort((a, b) => {
    const aTotalValue = parseFloat(a.querySelector('td:last-child').textContent.trim());
    const bTotalValue = parseFloat(b.querySelector('td:last-child').textContent.trim());
    return aTotalValue - bTotalValue;
});

document.querySelectorAll('#sendSmsAboutAuction').forEach((element) => {
    element.onclick = (event) => {
        const targetElement = event.target;
        const defaultValue = 'Dzień dobry, czy chcesz przebić najniższą ofertę w przetargu? Kliknij w link, aby zobaczyć szczegóły: https://mega1000.pl/firms/przetargi?firmToken=' + targetElement.classList[0] + '&orderId=' + targetElement.classList[1];

        // Prompt user for message input
        const message = prompt('Podaj treść wiadomości', defaultValue);

        if (message !== null) { // Check if prompt was not canceled
            const url = `https://admin.mega1000.pl/sms/send/${targetElement.classList[0]}?message=${encodeURIComponent(message)}&orderId=${targetElement.classList[1]}`;

            // Send fetch request
            fetch(url)
                .then(response => {
                    if (response.ok) {
                        Swal.fire('Wiadomość została wysłana', '', 'success');
                    } else {
                        Swal.fire('Błąd podczas wysyłania wiadomości', '', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error sending SMS:', error);
                    Swal.fire('Błąd podczas wysyłania wiadomości', '', 'error');
                });
        }
    };
});

const tableBody = table.querySelector('tbody');
tableBody.innerHTML = '';

rows.forEach(row => {
tableBody.appendChild(row);
});
</script>
</body>
