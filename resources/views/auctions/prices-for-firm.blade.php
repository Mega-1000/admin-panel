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
</head>

<body>
<div>
    @php
        $displayedFirmSymbols = [];
        $firmCounter = 1;
    @endphp
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
                        $displayedFirmSymbols = [];
                    @endphp

                    {{ dd($firms) }}
                    @foreach($firms as $firm)
                        @if(isset($auction) && $auction->offers->where('firm_id', $firm->firm->id)->count() === 0 || in_array($firm?->firm?->symbol ?? $firm?->symbol ?? [], $displayedFirmSymbols) || !isset($auction))
                            @continue
                        @endif

                        @php
                            $displayedFirmSymbols[] = $firm?->firm?->symbol ?? $firm->symbol ?? '';
                            $totalCost = 0;
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
                                            ->where('firm_id', $firm->firm->id)
                                            ->first();
                                    }
                                }

                                usort($offers, function($a, $b) {
                                    return $a->basic_price_net <=> $b->basic_price_net;
                                });

                                $totalCost += round((collect($offers)->min('basic_price_net') * 1.23), 2) *
                                    \App\Entities\OrderItem::where('order_id', $auction->chat->order->id)
                                        ->whereHas('product', function ($q) use ($product) {
                                            $q->where('product_group', $product->product_group);
                                        })->first()?->quantity;
                            @endphp
                        @endforeach

                        @php
                            $sortedFirms->push([
                                'firm' => $firm,
                                'totalCost' => round($totalCost / 3.33, 2)
                            ]);
                        @endphp
                    @endforeach

                    @foreach($sortedFirms->sortBy('totalCost') as $sortedFirm)
                        <tr>
                            <td>
                                firma
                                @if($firm->firm->id == request()->query('firmId'))
                                    <span style="color: red; font-weight: bold">
                                        {{ $firm->firm->name }}
                                    </span>
                                @else
                                    {{ $firmCounter }}
                                    @php
                                        $firmCounter++;
                                    @endphp
                                @endif
                            </td>

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
                                    @endphp

                                    @if(!empty($offers))
                                        @foreach($offers as $offer)
                                            {{ \App\Entities\Product::find($offer->product_id)->additional_info1 }}:
                                            {{ round($offer->basic_price_net * 1.23, 2) }}
                                            <br>
                                        @endforeach

                                        <span style="color: green">- Cena specjalna w przetargu</span>
                                    @else
                                        No offer
                                    @endif
                                </td>
                            @endforeach

                            <td>
                                {{ $sortedFirm['totalCost'] }}
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

$radius = $raw?->distance;

$distance = round($raw?->distance, 2);
}
                        @endphp

                        @if((isset($auction) && $auction?->offers->where('firm_id', $firm?->firm?->id ?? $firm->id ?? '')->count() ?? 1 === 0 && !in_array($symbol, $displayedFirmSymbols)) || (!in_array($symbol, $displayedFirmSymbols) && true))
                            <tr>
                                <td>
                                    firma
                                    @if($firm->firm->id == request()->query('firmId'))
                                        <span style="color: red; font-weight: bold">
                                            {{ $firm->firm->name }}
                                        </span>
                                    @else
                                        {{ $firmCounter }}
                                        @php
                                            $firmCounter++;
                                        @endphp
                                    @endif
                                </td>

                                @php
                                    $prices = [];
                                    $items = isset($auction) ? $auction?->chat?->order?->items : $order?->items;
                                    $totalCost = 0;

                                    foreach ($items as $item) {
                                        $variations = App\Entities\Product::where('product_group', $item->product->product_group)
                                            ->where('product_name_supplier', $symbol)
                                            ->get();

                                        $variations = $variations->sortBy(function($product) {
                                            return $product->price->gross_purchase_price_basic_unit_after_discounts;
                                        });

                                        $prices[] = $variations;

                                        $totalCost += $variations->min('price.net_special_price_basic_unit') * $item->quantity;
                                    }
                                @endphp

                                @foreach($prices as $price)
                                    <td>
                                        @foreach($price as $p)
                                            {{ $p->price->product->additional_info1 }}:
                                            {{ $p?->price->gross_purchase_price_basic_unit_after_discounts }}
                                            <br>
                                        @endforeach
                                    </td>
                                @endforeach

                                <td>
                                    {{ round($totalCost / 3.33, 2) }}
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

