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
                        $firmIdFromQuery = request()->query('firmId');
                    @endphp

                    @foreach($firms as $firm)
                        @php
                            $displayedFirmSymbols[] = $firm?->firm?->symbol ?? $firm?->symbol ?? '';
                            $totalCost = 0;
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
                                            ->where('firm_id', $firm->firm->id)
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

                                $totalCost += round(($minOffer * 1.23), 2) *
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

                    @php
                        $sortedFirms = $sortedFirms->sortBy('totalCost');
                    @endphp

                    @foreach($sortedFirms as $sortedFirm)
                        <tr>
                            <td>
                                firma
                                @if($sortedFirm['firm']->firm->id == $firmIdFromQuery)
                                    <span style="color: red; font-weight: bold">
                                       {{ $sortedFirm['firm']->firm->name }}
                                   </span>
                                @else
                                    {{ $firmCounter }}
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
                                        if ($auction->offers()->where('firm_id', $sortedFirm['firm']->firm->id)->whereHas('product', function ($q) use ($product) {$q->where('parent_id', $product->parent_id);})->first()) {
                                            $offers[] = \App\Entities\ChatAuctionOffer::whereHas('product', function ($q) use ($product) {$q->where('parent_id', $product->parent_id);})
                                                ->where('chat_auction_id', $auction->id)
                                                ->orderBy('basic_price_net', 'asc')
->where('firm_id', $sortedFirm['firm']->firm->id)
->first();
}

usort($offers, function($a, $b) {
return $a->basic_price_net <=> $b->basic_price_net;
});

$minOffer = collect($offers)->min('basic_price_net');

$totalCost = 0;
if (isset($auction) && $minOffer) {
$totalCost += $minOffer;
}

$missingData = !isset($auction);
@endphp

@if($missingData)
    <td>
        <h5 style="color:red">
            Brak informacji dla tej aukcji
        </h5>
    </td>
@else
    <td>
        {{ $sortedFirm['firm']['firm']['name'] ?? $sortedFirm['firm']['name'] }}
    </td>
    @endif
    </tr>
    <tr>
        <td>
            @if($missingData)
                <h5 style="color:red">
                    Brak informacji dla tej aukcji
                </h5>
            @else
                {{ $sortedFirm['totalCost'] }}
                <br>
            @endif
        </td>
    </tr>
    </table>
@endif
<div class="alert-success alert">
    Więcej informacji...
</div>
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

