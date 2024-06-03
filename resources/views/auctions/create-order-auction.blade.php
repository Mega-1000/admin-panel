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
                        $productPrice = empty(\App\Entities\ChatAuctionOffer::whereHas('product', function ($q) use ($product) {$q->where('parent_id', $product->parent_id);})
                                                        ->where('chat_auction_id', $order->chat->auctions->first()->id)
                                                        ->orderBy('basic_price_net', 'asc')
                                                        ->first()) ? $product->price?->gross_selling_price_basic_unit : \App\Entities\ChatAuctionOffer::whereHas('product', function ($q) use ($product) {$q->where('parent_id', $product->parent_id);})
                                                        ->where('chat_auction_id', $order->chat->auctions->first()->id)
                                                        ->orderBy('basic_price_net', 'asc')
                                                        ->first()->basic_price_net * 1.23;
                    @endphp
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            @if($item->count() > 1)
                                <input type="radio" data-product-id="{{ $product->id }}" name="product-group-{{ $loop->parent->index }}" class="mr-2 product-checkbox" data-price="{{ $productPrice }}" data-quantity="{{ $product->quantity }}" @if($loop->first) checked @endif>
                            @endif
                            <span class="product-text cursor-pointer" data-product-id="{{ $product->id }}">
                                Nazwa produktu: {{ $product->name }} <br>
                                Ilość m3: {{ round($product->quantity / 3.33, 2) }} <br>
                                Wartość brutto: {{ $productPrice }}
                            </span>
                            <button class="ml-2 remove-product-btn bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded" data-product-id="{{ $product->id }}">Usuń</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
        <div class="mt-6 flex justify-between">
            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" id="cash-on-delivery" class="form-checkbox">
                    <span class="ml-2">Zapłata przy odbiorze przelewem błyskawicznym</span> <span id="cash-on-delivery-amount" class="ml-2"></span>
                </label>
            </div>
            <div>
                <h2 class="text-xl font-bold mb-2">Wartość brutto całej oferty:</h2>
                <p class="total-price">$0</p>
                <div id="payment-info" class="hidden">
                    <p>Do zapłaty teraz: <span id="pay-now-amount"></span> zł</p>
                    <p>Do zapłaty przy odbiorze: <span id="remaining-payment">0</span> zł</p>
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

    document.getElementById('cash-on-delivery').addEventListener('change', function() {
        updateTotalPrice();
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
                    const price = parseFloat(span.textContent.match(/Cena: ([\d\.]+)/)[1]);
                    totalPrice += price * quantity;
                });
            }
        });

        const totalAmount = (totalPrice / 3.33).toFixed(2);
        document.querySelector('.total-price').textContent = totalAmount + 'ZŁ';

        const cashOnDelivery = document.getElementById('cash-on-delivery').checked;
        const paymentInfo = document.getElementById('payment-info');
        const cashOnDeliveryAmount = document.getElementById('cash-on-delivery-amount');
        const payNowAmount = document.getElementById('pay-now-amount');
        if (cashOnDelivery) {
            paymentInfo.classList.remove('hidden');
            const payNow = (totalAmount * 0.1).toFixed(2);
            payNowAmount.textContent = payNow + ' zł';
            cashOnDeliveryAmount.textContent = `(${(totalAmount- payNow} zł)`;
            document.getElementById('remaining-payment').textContent = (totalAmount - payNow).toFixed(2) + ' zł';
        } else {
            paymentInfo.classList.add('hidden');
            cashOnDeliveryAmount.textContent = '';
        }
    }

    updateTotalPrice();

    const sendOrderButton = document.querySelector('button');
    sendOrderButton.addEventListener('click', sendOrder);

    function sendOrder() {
        Swal.fire('Ładowanie...', '');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const totalPrice = parseFloat(document.querySelector('.total-price').textContent.replace('ZŁ', ''));
        const productData = [];
        const cashOnDelivery = document.querySelector('#cash-on-delivery').checked;

        const checkedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
        checkedCheckboxes.forEach(function(checkedCheckbox) {
            const productId = checkedCheckbox.getAttribute('data-product-id');
            const quantity = parseInt(checkedCheckbox.dataset.quantity);
            productData.push({ productId, quantity });
        });

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
                if (cashOnDelivery) {
                    message += ' Zapłata nastąpi przy odbiorze przelewem błyskawicznym.';
                } else {
                    message += ' Zostaniesz przekierowany do banku.';
                    window.location.href = `https://mega1000.pl/payment?token={{ $order->token }}&total=${document.querySelector('#cash-on-delivery').checked ? (totalPrice * 0.1).toFixed(2) : (totalPrice + 50)}`;
                }
                await Swal.fire('Sukces', message, 'success');
            })
            .catch(error => {
                console.error('Error:', error);
                // Handle the error here
            });
    }
</script>
</body>

</html>
