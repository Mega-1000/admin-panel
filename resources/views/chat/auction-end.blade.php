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

        th.asc::after {
            content: " ↓"; /* Change to arrow SVG or symbol as needed */
        }

        th.desc::after {
            content: " ↑"; /* Change to arrow SVG or symbol as needed */
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
                    let rows = table.rows;
                    for (let i = 1; i < (rows.length - 1); i++) {
                        let shouldSwitch = false;
                        let x = rows[i].getElementsByTagName("TD")[n];
                        let y = rows[i + 1].getElementsByTagName("TD")[n];
                        shouldSwitch = shouldSwitchRows(x, y, dir);
                        if (shouldSwitch) {
                            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                            switching = true;
                            switchcount++;
                        } else if (switchcount === 0 && dir === "asc") {
                            dir = "desc";
                            switching = true;
                        }
                    }
                }
            }

            function shouldSwitchRows(x, y, dir) {
                if (dir === "asc") {
                    return x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase();
                } else if (dir === "desc") {
                    return x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase();
                }
                return false;
            }

            const order = {};

            window.onload = () => {
                const offerCheckboxes = document.querySelectorAll('.offer-checkbox');
                offerCheckboxes.forEach((checkbox) => {
                    checkbox.addEventListener('change', (e) => {
                        if (e.target.checked) {
                            if (order[e.target.dataset.firm]) {
                                order[e.target.dataset.firm].push(e.target.dataset.product);
                            } else {
                                order[e.target.dataset.firm] = [e.target.dataset.product];
                            }
                        } else {
                            delete order[e.target.dataset.firm];
                        }
                        console.log(order);
                    });
                });

                const submitButton = document.querySelector('#submit-button');
                submitButton.addEventListener('click', () => {
                    const form = createForm(order);
                    document.body.appendChild(form);
                    form.submit();
                });

                const headers = document.querySelectorAll("th");
                headers.forEach((header, i) => {
                    header.addEventListener('click', () => sortTable(i));
                });

                headers[1].click();
            };

            function createForm(order) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('auctions.end-create-orders', ['auction' => $auction->id]) }}';
                form.style.display = 'none';

                const orderInput = document.createElement('input');
                orderInput.name = 'order';
                orderInput.value = JSON.stringify(order);
                form.appendChild(orderInput);

                const csrfInput = document.createElement('input');
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                return form;
            }

        })();
    </script>

</head>

<body>
<div>
    <div class="container" id="flex-container">
        <div id="chat-container">
            <table>
                <thead>
                <tr>
                    <th>
                        <h5 style="text-align: right">
                            Ceny za m3
                        </h5>
                    </th> <!-- Empty cell for the top-left corner -->
                    @foreach($products as $product)
                        <th>
                            @php
                                $name = $product->product->name;
                                $words = explode(' ', $name);
                                array_shift($words);
                                $name = implode(' ', $words);
                            @endphp
                            {{ $name }}
                            <button class="btn btn-primary">
                                Sortuj
                            </button>
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($firms as $firm)
                    @if($auction->offers->where('firm_id', $firm->id)->count() === 0)
                        @continue
                    @endif
                    <tr>
                        <td>
                            {{ $firm->firm->symbol }}
                        </td> <!-- Modify this according to your firm object -->
                        @foreach($products as $product)
                            <td>
                                @if($offer = $auction->offers->where('firm_id', $firm->id)->where('order_item_id', $product->id)->first())
                                    {{ $auction->offers->where('firm_id', $firm->id)->where('order_item_id', $product->id)->min('basic_price_net') }} Zł

                                    <input type="checkbox" class="offer-checkbox" id="offer-checkbox{{ $offer->id }}" data-firm="{{ $firm->firm->name }}" data-product="{{ $product->product->name }}">
                                @else
                                    No offer
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>

            <button class="btn btn-primary mt-2 mb-5" id="submit-button">
                Wyślij zamówienie
            </button>
        </div>

        <table class="mb-5">
            <thead>
            <tr>
                <th>
                    <h5 style="text-align: right">
                        Firmy, które nie złożyły oferty
                    </h5>
                </th>
            </tr>
            </thead>
            <tbody>

            @foreach($firms as $firm)
                @if($auction->offers->where('firm_id', $firm->id)->count() === 0)
                    <tr>
                        <td>
                            {{ $firm->firm->symbol }} <!-- Modify this according to your firm object -->
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
