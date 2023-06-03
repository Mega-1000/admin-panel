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
        window.onload = function() {
            window.order = {};

            document.querySelectorAll('.offer-checkbox').forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    if (this.checked) {
                        // if firm have product alredy in order make it array and push new product
                        if (window.order[this.dataset.firm]) {
                            window.order[this.dataset.firm].push(this.dataset.product);
                        } else {
                            window.order[this.dataset.firm] = [this.dataset.product];
                        }
                    } else {
                        delete window.order[this.dataset.firm];
                    }

                    console.log(window.order);
                });
            });

            document.querySelector('#submit-button').addEventListener('click', () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('auctions.end-create-orders', ['auction' => $auction->id]) }}';
                form.style.display = 'none';

                const orderInput = document.createElement('input');
                orderInput.name = 'order';
                orderInput.value = JSON.stringify(window.order);
                form.appendChild(orderInput);

                const csrfInput = document.createElement('input');
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            });
        };
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
                        <th>{{ $product->product->name }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($firms as $firm)
                    <tr>
                        <td>{{ $firm->firm->symbol }}</td> <!-- Modify this according to your firm object -->
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
    </div>
</div>
</body>
</html>
