<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.chat_name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 40%; margin: auto; }
        @media screen and (max-width: 768px) { .container { width: 100%; } }
        table { border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        td { vertical-align: middle; }
        #chat-container { margin-top: 50px; }
    </style>
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

            <div class="alert-success alert">
                Poleć naszą platformę znajomym, a my zaoferujemy Ci 30zł zniżki za każdego nowego użytkownika!
                <br>
                Wystarczy podać numer telefonu!
                <br>
                <br>
                <a href="https://mega1000.pl/polec-znajomego" target="_blank" class="btn btn-primary">
                    Zobacz więcej na temat promocji
                </a>
            </div>

            @if($firms->isEmpty())
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
                        @endforeach
                        <th>Wartość oferty w przypadku wybrania najtańszych opcji</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $sortedFirms = collect();
                        $displayedFirmSymbols = [];
                        $orderItems = $order->items->keyBy('product.product_group');
                        $auctionOffers = isset($auction) ? $auction->offers->groupBy('firm_id') : collect();
                    @endphp

                    @foreach($firms as $firm)
                        @php
                            $firmSymbol = $firm->firm->symbol ?? $firm->symbol ?? '';
                            if ((isset($auction) && empty($auctionOffers[$firm->firm->id])) || in_array($firmSymbol, $displayedFirmSymbols) || !isset($auction)) {
                                continue;
                            }
                            $displayedFirmSymbols[] = $firmSymbol;
                            $totalCost = 0;
                        @endphp

                        <tr>
                            <td>
                                {{ $firmSymbol }}
                                <br>
                                Odległość: {{ round($firm->distance) }} KM
                                <br>
                                @php
                                    $employee = \App\Helpers\LocationHelper::getNearestEmployeeOfFirm($order->customer, $firm->firm);
                                @endphp
                                @if($employee && $employee->phone && auth()->id())
                                    tel przedstawiciela: <br> +48 {{ $employee->phone }} {{ $employee->firstname }}
                                @endif
                            </td>

                            @foreach($products as $product)
                                <td>
                                    @php
                                        $offers = $auctionOffers[$firm->firm->id] ?? collect();
                                        $productOffers = $offers->where('product.product_group', $product->product->product_group)->sortBy('basic_price_net');
                                        $minOffer = $productOffers->first();
                                        $minOfferPrice = $minOffer ? round($minOffer->basic_price_net * 1.23, 2) : null;
                                        $orderItem = $orderItems[$product->product->product_group] ?? null;
                                        $totalCost += ($minOfferPrice * ($orderItem->quantity ?? 0)) * ($product->packing->numbers_of_basic_commercial_units_in_pack ?? 0.33333);
                                    @endphp

                                    @forelse($productOffers as $offer)
                                        {{ $offer->product->additional_info1 }}:
                                        {{ round($offer->basic_price_net * 1.23, 2) }}
                                        @if(auth()->id())
                                            ({{ $offer->basic_price_net }})
                                        @endif
                                        <br>
                                    @empty
                                        No offer
                                    @endforelse
                                    @if($productOffers->isNotEmpty())
                                        <span style="color: green">- specjalnie dla ciebie</span>
                                    @endif
                                </td>
                            @endforeach

                            <td>
                                {{ round($totalCost, 2) }}
                                <br>
                                <a class="btn btn-primary" href="https://admin.mega1000.pl/make-order/{{ $firmSymbol }}/{{ $order->id }}">
                                    Wyślij zamówienie na tego producenta
                                </a>

                                @if(auth()->id())
                                    <button class="{{ $firm->token }} {{ $order->id }} btn btn-primary send-sms-button">
                                        Wyślij smsa do przedstawiciela w sprawie przetargu
                                    </button>

                                    <a class="btn btn-secondary" href="https://admin.mega1000.pl/auctions/offer/create/{{ $firm->token }}">
                                        Dodaj cenę jako ta firma
                                    </a>
                                @endif
                            </td>
                        </tr>

                        @php
                            $sortedFirms->push([
                                'firm' => $firm,
                                'totalCost' => round($totalCost, 2)
                            ]);
                        @endphp
                    @endforeach

                    @foreach($firms as $firm)
                        @if(in_array($firm->firm->symbol ?? $firm->symbol ?? '', $displayedFirmSymbols) || !isset($auction))
                            @continue
                        @endif

                        @php
                            $symbol = $firm->firm->symbol ?? $firm->symbol ?? '';
                            $coordinatesOfUser = \DB::table('postal_code_lat_lon')->where('postal_code', $order->getDeliveryAddress()->postal_code)->first();

                            $distance = 'N/A';
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

                                $distance = round($raw->distance ?? 'N/A', 2);
                            }
                        @endphp

                        @if((isset($auction) && ($auction->offers->where('firm_id', $firm->firm->id ?? $firm->id ?? '')->count() ?? 1) === 0 && !in_array($symbol, $displayedFirmSymbols)) || (!in_array($symbol, $displayedFirmSymbols) && true))
                            <tr>
                                <td>
                                    {{ $symbol }}
                                    <br>
                                    Odległość: {{ $distance }} KM
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
                                    $items = isset($auction) ? $auction->chat->order->items : $order->items;
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
                                                {{ round($p->price->gross_selling_price_basic_unit, 2) }}
                                                @if(auth()->id())
                                                    ({{ round($p->price->gross_selling_price_basic_unit / 1.23, 2) }})
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
                                            <button class="{{ App\Entities\ChatAuctionFirm::where('firm_id', App\Entities\Firm::where('symbol', $symbol)->first()->id)->where('chat_auction_id', $order->chat->auctions->first()->id)->first()?->token }} {{ $order->id }} btn btn-primary send-sms-button">
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.querySelector('table');
        if (!table) return;

        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            const aTotalValue = parseFloat(a.querySelector('td:last-child').textContent.trim()) || Infinity;
            const bTotalValue = parseFloat(b.querySelector('td:last-child').textContent.trim()) || Infinity;
            return aTotalValue - bTotalValue;
        });

        tbody.append(...rows);

        document.querySelectorAll('.send-sms-button').forEach((element) => {
            element.addEventListener('click', function(event) {
                const [token, orderId] = this.className.split(' ');
                const defaultValue = `Dzień dobry, czy chcesz przebić najniższą ofertę w przetargu? Kliknij w link, aby zobaczyć szczegóły: https://mega1000.pl/firms/przetargi?firmToken=${token}&orderId=${orderId}`;

                const message = prompt('Podaj treść wiadomości', defaultValue);

                if (message) {
                    const url = `https://admin.mega1000.pl/sms/send/${token}?message=${encodeURIComponent(message)}&orderId=${orderId}`;

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
            });
        });
    });
</script>
</body>
</html>
