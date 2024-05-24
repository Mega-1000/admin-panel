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
        @foreach($finalItems as $item)
            <div class="border-b py-4">
                @foreach($item as $product)
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <input type="checkbox" class="mr-2" @if($item->count() === 1) disabled @endif>
                            <span>
                                    Nazwa produktu: {{ $product->name }} <br>
                                    Ilość: {{ $product->quantity }} <br>
                                    Cena: {{ empty(\App\Entities\ChatAuctionOffer::where('product_id', $product->id)->first()?->commercial_price_gross) ? $product->price?->gross_selling_price_basic_unit : \App\Entities\ChatAuctionOffer::where('product_id', $product->id)->first()?->commercial_price_gross }}
                                </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
        <div class="mt-6 flex justify-between">
            <div>
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Continue Shopping
                </button>
            </div>
            <div>
                <h2 class="text-xl font-bold mb-2">Total:</h2>
                <p>$99.99</p> <!-- Replace with actual total price calculation -->
                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-2">
                    Proceed to Checkout
                </button>
            </div>
        </div>
    </div>
</div>
</body>

</html>
