<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.chat_name') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Additional styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
                <h1 class="text-3xl font-bold mb-6">Wysyłanie zamówienia</h1>
                <div class="bg-white rounded-lg shadow-md p-6">
                    @php
                        $totalPrice = 0;
                    @endphp
                    @foreach($finalItems as $item)
                        <div class="border-b py-4">
                            @foreach($item as $product)
                                @php
                                    $productPrice = (empty(\App\Entities\ChatAuctionOffer::whereHas('product', function ($q) use ($product) {$q->where('parent_id', $product->parent_id);})
                                                                    ->where('chat_auction_id', $order->chat->auctions->first()->id)
                                                                    ->orderBy('basic_price_net', 'asc')
                                                                    ->first()) ? $product->price?->gross_selling_price_basic_unit : \App\Entities\ChatAuctionOffer::whereHas('product', function ($q) use ($product) {$q->where('parent_id', $product->parent_id);})
                                                                    ->where('chat_auction_id', $order->chat->auctions->first()->id)
                                                                    ->orderBy('basic_price_net', 'asc')
                                                                    ->first()->basic_price_net * 1.23) * $product->number_of_sale_units_in_the_pack;
                                @endphp
                            {{$product->number_of_sale_units_in_the_pack}}
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        @if($item->count() > 1)
                                            <input type="radio" data-product-id="{{ $product->id }}" name="product-group-{{ $loop->parent->index }}" class="mr-2 product-checkbox" data-price="{{ $productPrice }}" data-quantity="{{ $product->quantity }}" @if($loop->first) checked @endif>
                                        @endif
                                        <span class="product-text cursor-pointer" data-product-id="{{ $product->id }}" data-quantity="{{$product->quantity}}">
                                Nazwa produktu: {{ $product->name }} <br>
                                Ilość m3: {{ round($product->quantity * $product->packing->numbers_of_basic_commercial_units_in_pack, 2) }} <br>
                                Cena brutto: {{ $productPrice }}
                            </span>
                                        <button class="ml-2 remove-product-btn bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded" data-product-id="{{ $product->id }}">Usuń</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    <div class="mt-6 flex justify-between">
                        <div>
                            <h3 style="font-weight: bold; font-size: larger"> Sposoby płatności: </h3>
                            a)  10 % przedpłata na konto <br>
                            90 % przy odbiorze towaru przelewem błyskawicznym. <br>
                            Klient zobowiązuje się do przelewu który w przeciągu 30 minut pojawi się na naszym koncie <br>
                            Prosimy wziąść pod uwage możliwości braku sieci i innych przypadków losowych. <br>
                            W przypadku braku wpływu na nasze konto klient ponosi odpowiedzialność wszelkich kosztów z tego wynikających.
                            <br>
                            Dodakowy koszt skorzystania z takiej opcji to 100 zł
                            <br>
                            <br>
                            b) 100 % przedpłata przed dostawą. <br>
                            Klient zobowiązuje się tylko do rozładunku w wspólnie ustalonym terminie z fabryką.
                            <br>
                            <br>

                            <h3 style="font-weight: bold; font-size: large; color: red">Wybierz jedną opcję: </h3>
                            <label class="inline-flex items-center">
                                <input type="radio" id="full-payment" name="payment-option" class="form-radio" checked>
                                <span class="ml-2">100 % przedpłata przed dostawą</span>
                            </label>
                            <br>
                            <label class="inline-flex items-center">
                                <input type="radio" id="cash-on-delivery" name="payment-option" class="form-radio">
                                <span class="ml-2">Zapłata przy odbiorze przelewem błyskawicznym</span>
                            </label>
                            <br>
                            <br>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold mb-2">Końcowa wartość oferty brutto:</h2>
                            <p class="total-price">$0</p>
                            <div id="payment-info" class="hidden">
                                <p>Do zapłaty teraz: <span id="pobranie"></span> zł</p>
                                <p>Do zapłaty przy odbiorze: <span id="remaining-payment">0</span> zł</p>
                                <p>Dopłata do płatności przy dostawie: 100 zł</p>
                            </div>
                            <button onclick="sendOrder()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-2">
                                Wyślij zamówienie
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.querySelectorAll('.product-checkbox').forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        updateTotalPrice();
                    });
                });
                document.querySelectorAll('.product-text').forEach(function(productText) {
                    productText.addEventListener('click', function() {
                        const productId = this.getAttribute('data-product-id');
                        const checkbox = this.parentNode.querySelector('.product-checkbox');
                        if (checkbox) {
                            checkbox.checked = true;
                            updateTotalPrice();
                        }
                    });
                });

                document.querySelectorAll('.remove-product-btn').forEach(function(removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        const productId = this.getAttribute('data-product-id');
                        const productGroup = this.closest('.border-b');
                        productGroup.remove();
                        updateTotalPrice();
                    });
                });

                document.querySelectorAll('input[name="payment-option"]').forEach(function(paymentOption) {
                    paymentOption.addEventListener('change', function() {
                        updateTotalPrice();
                    });
                });

                function updateTotalPrice() {
                    let totalPrice = 0;
                    const productGroups = Array.from(document.querySelectorAll('.border-b'));

                    productGroups.forEach(function(productGroup) {
                        const checkedCheckbox = productGroup.querySelector('.product-checkbox:checked');
                        if (checkedCheckbox) {
                            const productId = checkedCheckbox.closest('.border-b').querySelector('span').getAttribute('data-product-id');
                            const checkedPrice = parseFloat(checkedCheckbox.dataset.price);
                            const checkedQuantity = parseInt(checkedCheckbox.dataset.quantity);
                            totalPrice += checkedPrice * checkedQuantity;
                        } else {
                            const products = Array.from(productGroup.querySelectorAll('span[data-product-id]'));
                            products.forEach(span => {
                                const productId = span.getAttribute('data-product-id');
                                const quantity = parseInt(span.textContent.match(/Ilość m3: ([\d\.]+)/)[1] * 3.33);
                                const price = parseFloat(span.textContent.match(/Cena brutto: ([\d\.]+)/)[1]);
                                totalPrice += price * quantity;
                            });
                        }
                    });

                    const totalAmount = (totalPrice).toFixed(2);
                    document.querySelector('.total-price').textContent = totalAmount +'ZŁ';
                    const cashOnDelivery = document.getElementById('cash-on-delivery').checked;
                    const paymentInfo = document.getElementById('payment-info');
                    if (cashOnDelivery) {
                        paymentInfo.classList.remove('hidden');
                        document.getElementById('pobranie').textContent = (totalAmount / 10).toFixed(2);
                        document.getElementById('remaining-payment').textContent = (totalAmount - totalAmount / 10).toFixed(2);
                    } else {
                        paymentInfo.classList.add('hidden');
                    }
                }

                updateTotalPrice();

                const sendOrderButton = document.querySelector('button');
                sendOrderButton.addEventListener('click', sendOrder);

                function sendOrder() {
                    Swal.fire('Ładowanie...', '');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    let totalPrice = parseFloat(document.querySelector('.total-price').textContent.replace('ZŁ', ''));
                    const productData = [];
                    const cashOnDelivery = document.querySelector('#cash-on-delivery').checked;

                    const checkedCheckboxes = document.querySelectorAll('.product-checkbox:checked');

                    // If no checkboxes are found, assume there's a single product
                    if (checkedCheckboxes.length === 0) {
                        const singleProduct = document.querySelector('.product-text');
                        if (singleProduct) {
                            const productId = singleProduct.getAttribute('data-product-id');
                            const quantity = parseInt(singleProduct.getAttribute('data-quantity'));
                            productData.push({ productId, quantity });
                        }
                    } else {
                        checkedCheckboxes.forEach(function(checkedCheckbox) {
                            const productId = checkedCheckbox.getAttribute('data-product-id');
                            const quantity = parseInt(checkedCheckbox.getAttribute('data-quantity'));
                            productData.push({ productId, quantity });
                        });
                    }

                    if (productData.length === 0) {
                        Swal.fire('Błąd', 'Proszę wybrać co najmniej jeden produkt', 'error');
                        return;
                    }


                    fetch('/submit-order/{{ $order->id }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ totalPrice, productData, cashOnDelivery })
                    })
                        .then(async (data) => {
                            let message = 'Pomyślnie złożono zamówienie.';
                            message += ' Zostaniesz przekierowany do banku.';

                            if (cashOnDelivery) {
                                totalPrice = totalPrice / 10;
                            }

                            await Swal.fire('Sukces', message, 'success');

                            window.location.href = `https://mega1000.pl/payment?token={{ $order->token }}&total=${totalPrice + 50}`;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Handle the error here
                        });
                }
            </script>
</body>
</html>

