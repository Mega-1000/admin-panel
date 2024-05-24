<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
    <h1 class="text-3xl font-bold mb-6">Checkout</h1>
    <div class="bg-white rounded-lg shadow-md p-6">
        @php
            $totalPrice = 0;
        @endphp
        @foreach($finalItems as $item)
            <div class="border-b py-4">
                @foreach($item as $product)
                    @php
                        $productPrice = empty(\App\Entities\ChatAuctionOffer::where('product_id', $product->id)->first()?->commercial_price_gross) ? $product->price?->gross_selling_price_basic_unit : \App\Entities\ChatAuctionOffer::where('product_id', $product->id)->first()?->commercial_price_gross;
                    @endphp
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            @if($item->count() > 1)
                                <input type="radio" name="product-group-{{ $loop->parent->index }}" class="mr-2 product-checkbox" data-price="{{ $productPrice }}" data-quantity="{{ $product->quantity }}" @if($loop->first) checked @endif>
                            @endif
                            <span>
                                    Nazwa produktu: {{ $product->name }} <br>
                                    Ilość m3: {{ round($product->quantity / 3.33, 2) }} <br>
                                    Cena: {{ $productPrice }}
                                </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
        <div class="mt-6 flex justify-between">
            <div>

            </div>
            <div>
                <h2 class="text-xl font-bold mb-2">Końcowa cena:</h2>
                <p class="total-price">$0</p>
                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-2">
                    Wyślij zamówienie
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.product-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            updateTotalPrice();
        });
    });

    function updateTotalPrice() {
        let totalPrice = 0;
        const productGroups = Array.from(document.querySelectorAll('.border-b'));

        productGroups.forEach(function(productGroup) {
            const checkedCheckbox = productGroup.querySelector('.product-checkbox:checked');
            if (checkedCheckbox) {
                const checkedPrice = parseFloat(checkedCheckbox.dataset.price);
                const checkedQuantity = parseInt(checkedCheckbox.dataset.quantity);
                totalPrice += checkedPrice * checkedQuantity;
            } else {
                const productPrices = Array.from(productGroup.querySelectorAll('.product-checkbox')).map(cb => parseFloat(cb.dataset.price));
                const productQuantities = Array.from(productGroup.querySelectorAll('.product-checkbox')).map(cb => parseInt(cb.dataset.quantity));
                const groupTotalPrice = productPrices.reduce((sum, price, index) => sum + price * productQuantities[index], 0);
                totalPrice += groupTotalPrice;
            }
        });

        document.querySelector('.total-price').textContent = (totalPrice / 3.33).toFixed(2) + 'ZŁ';
    }

    updateTotalPrice();
</script>
</body>

</html>
