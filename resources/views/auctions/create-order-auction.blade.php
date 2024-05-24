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
</head>

<body>
    @foreach($finalItems as $item)
        <div>
            @foreach($item as $product)
                <span>
                    Nazwa produktu: {{ $product->name }} Ilość: {{ $product->quantity }} Cena:
                    {{ !!\App\Entities\ChatAuctionOffer::where('product_id', $product->id)->first()?->commercial_price_gross }} {{ $product->price?->basic_price_gross }}
                    @if($item->count() > 1) <input type="checkbox"> @endif
                </span>
            @endforeach
            <br>
        </div>
    @endforeach
</body>
