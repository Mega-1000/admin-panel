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

            @if($firms->count() == 0)
                <div class="text-center">
                    <h1>Tu za nie długo zaczną wyświetlać się wyniki twojego przetargu.</h1>
                </div>
            @else
                <table>
                    <tbody>
                    @php
                        $sortedFirms = collect();
                        $displayedFirmSymbols = [];
                    @endphp

                    <div class=" mx-auto px-4 py-8">
                        @if(session()->get('success'))
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                                <p>Pomyślnie stworzono zamówienie i dodano przedstawicieli do chatu</p>
                            </div>
                        @endif

                        <div class="bg-white shadow-md rounded-lg overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-800">Oglądasz tabele zapytania: {{ $order->id }}</h2>
                            </div>

                            <div class="p-6 bg-blue-50 border-b border-blue-200">
                                <p class="text-blue-700 mb-4">Poleć naszą platformę znajomym, a my zaoferujemy Ci 30zł zniżki za każdego nowego użytkownika!</p>
                                <p class="text-blue-700 mb-4">Wystarczy podać numer telefonu!</p>
                                <a href="https://mega1000.pl/polec-znajomego" target="_blank" class="inline-block px-4 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition duration-300">
                                    Zobacz więcej na temat promocji
                                </a>
                            </div>

                            @if($firms->count() == 0)
                                <div class="text-center py-8">
                                    <h1 class="text-2xl font-bold text-gray-700">Tu za niedługo zaczną wyświetlać się wyniki twojego przetargu.</h1>
                                </div>
                            @else
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ceny brutto za m3
                                        </th>
                                        @foreach($products as $product)
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                @php
                                                    $name = $product->product->name;
                                                    $words = explode(' ', $name);
                                                    array_shift($words);
                                                    $name = implode(' ', $words);
                                                @endphp
                                                {{ $name }}
                                            </th>
                                        @endforeach
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Wartość oferty w przypadku wybrania najtańszych opcji
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($firms as $firm)
                                        @if(isset($auction) && $auction->offers->where('firm_id', $firm->firm->id)->count() === 0 || in_array($firm?->firm?->symbol ?? $firm?->symbol ?? [], $displayedFirmSymbols) || !isset($auction))
                                            @continue
                                        @endif

                                        @php
                                            $displayedFirmSymbols[] = $firm?->firm?->symbol ?? $firm->symbol ?? '';
                                            $totalCost = 0;
                                            $missingData = false;
                                        @endphp

                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $firm?->firm?->symbol ?? $firm->symbol ?? '' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Odległość: {{ round($firm->distance) }} KM
                                                </div>
                                                @php
                                                    $employee = \App\Helpers\LocationHelper::getNearestEmployeeOfFirm($order->customer, $firm->firm);
                                                @endphp
                                                @if($employee && $employee->phone && auth()->id())
                                                    <div class="text-sm text-gray-500">
                                                        Tel przedstawiciela: +48 {{ $employee->phone }}
                                                    </div>
                                                @endif
                                            </td>

                                            @foreach($products as $product)
                                                <td class="px-6 py-4 whitespace-nowrap">
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

                                                    @if(!empty($offers))
                                                        @foreach($offers as $offer)
                                                            <div class="text-sm text-gray-900">
                                                                {{ \App\Entities\Product::find($offer->product_id)->additional_info1 }}:
                                                                {{ round($offer->basic_price_net * 1.23, 2) }}
                                                                @if(auth()->id())
                                                                    <span class="text-gray-500">({{ $offer->basic_price_net }})</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                        <div class="text-sm text-green-600 mt-1">- specjalnie dla ciebie</div>
                                                    @else
                                                        <div class="text-sm text-gray-500">No offer</div>
                                                    @endif
                                                </td>
                                            @endforeach

                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ round($totalCost, 2) }}
                                                </div>
                                                <a href="https://admin.mega1000.pl/make-order/{{ $firm?->firm?->symbol }}/{{ $order->id }}" class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    Wyślij zamówienie
                                                </a>

                                                @if(auth()->id())
                                                    <button class="{{ App\Entities\ChatAuctionFirm::where('firm_id', App\Entities\Firm::where('symbol', $firm?->firm?->symbol)->first()->id)->where('chat_auction_id', $order->chat->auctions->first()->id)->first()?->token }} {{ $order->id }} mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" id="sendSmsAboutAuction">
                                                        Wyślij SMS
                                                    </button>

                                                    <a href="https://admin.mega1000.pl/auctions/offer/create/{{ App\Entities\ChatAuctionFirm::where('firm_id', App\Entities\Firm::where('symbol', $firm?->firm?->symbol)->first()->id)->where('chat_auction_id', $order->chat->auctions->first()->id)->first()?->token }}" class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                        Dodaj cenę
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
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $symbol }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        Odległość: {{ $distance ?? 'N/A' }} KM
                                                    </div>
                                                    @php
                                                        $employee = \App\Entities\Employee::where('email', $firm->email_of_employee)->first();
                                                    @endphp
                                                    @if($employee && $employee->phone && auth()->id())
                                                        <div class="text-sm text-gray-500">
                                                            Tel przedstawiciela: +48 {{ $employee->phone }}
                                                        </div>
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
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-500">No offer</div>
                                                        </td>
                                                    @endforeach
                                                @else
                                                    @foreach($prices as $price)
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            @foreach($price as $p)
                                                                <div class="text-sm text-gray-900">
                                                                    {{ $p->price->product->additional_info1 }}:
                                                                    {{ round($p?->price->gross_selling_price_basic_unit, 2) }}
                                                                    @if(auth()->id())
                                                                        <span class="text-gray-500">({{ round($p?->price->gross_selling_price_basic_unit / 1.23, 2) }})</span>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </td>
                                                    @endforeach
                                                @endif

                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($missingData)
                                                        <div class="text-sm text-gray-500">Missing data</div>
                                                    @else
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ round($totalCost, 2) }}
                                                        </div>
                                                        <a href="https://admin.mega1000.pl/make-order/{{ $symbol }}/{{ $order->id }}" class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                            Wyślij zamówienie
                                                        </a>

                                                        @if(auth()->id())
                                                            <button class="{{ App\Entities\ChatAuctionFirm::where('firm_id', App\Entities\Firm::where('symbol', $symbol)->first()->id)->where('chat_auction_id', $order->chat->auctions->first()->id)->first()?->token }} {{ $order->id }} mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" id="sendSmsAboutAuction">
                                                                Wyślij SMS
                                                            </button>

                                                            <a href="https://admin.mega1000.pl/auctions/offer/create/{{ App\Entities\ChatAuctionFirm::where('firm_id', App\Entities\Firm::where('symbol', $symbol)->first()->id)->where('chat_auction_id', $order->chat->auctions->first()->id)->first()?->token }}" class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                                Dodaj cenę
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
