<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.chat_name') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.css" rel="stylesheet" />

    <style>
        .slider {
            accent-color: #3b82f6; /* Customize the accent color */
        }
    </style>
</head>

<body class="bg-gray-100 flex justify-center items-center min-h-screen">
<div class="max-w-lg mx-auto bg-white rounded-lg shadow-md p-6">
    <form action="{{ route('auctions.store', ['chat' => $chat->id, 'backUrl' => URL::previous()]) }}" method="post">
        @csrf

        @php
            $previousVariationGroups = $chat->order->customer->orders() ->whereHas('items.product') ->with('items.product') ->get() ->pluck('items.*.product.product_group') ->flatten() ->unique();

            $duplicateOrders = $chat->order->customer->orders()
                ->whereHas('items', function ($q) use ($previousVariationGroups) {
                        $q->whereHas('product', function ($q) use ($previousVariationGroups) {
                        $q->whereIn('product_group', $previousVariationGroups);
                    });
                })
                ->where('id', '!=', $chat->order->id)
                ->where('created_at', '>', now()->subDays(2))
                ->whereHas('chat', function ($q) {
                    $q->whereHas('auctions');
                })
                ->first();
        @endphp
        @if(empty($duplicateOrders))
        <div class="mb-4">
            <label for="end_of_auction" class="block mb-2 text-sm font-medium text-gray-700">Data zakończenia przetargu</label>
            <input type="datetime-local" id="end_of_auction" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('end_of_auction') border-red-500 @enderror" name="end_of_auction" value="{{ old('end_of_auction', now()->addDays(3)->setTime(15, 00)) }}">
            @error('end_of_auction')
            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="notes" class="block mb-2 text-sm font-medium text-gray-700">Dodatkowe informację do wzięcia pod uwagę przez firmy uczestniczące w przetargu</label>
            <textarea id="notes" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('notes') border-red-500 @enderror" name="notes">{{ old('notes') }}</textarea>
            @error('notes')
            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="price-quality-slider" class="block mb-2 text-sm font-medium text-gray-700">Podział procentowy cena/jakość (przesuń suwak aby zmienić ten parametr)</label>
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-500">Cena</span>
                <span class="text-sm text-gray-500">Jakość</span>
            </div>
            <input type="range" id="price-quality-slider" class="slider w-full @error('price') border-red-500 @enderror @error('quality') border-red-500 @enderror" min="0" max="100" value="{{ old('price', 50) }}" oninput="updateValues()">
            <div class="flex justify-between mt-2">
                <input type="text" id="price-input" value="{{ old('price', 50) }}" name="price" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-1/2 p-2.5" placeholder="Cena" readonly>
                <input type="text" id="quality-input" value="{{ old('quality', 50) }}" name="quality" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-1/2 p-2.5" placeholder="Jakość" readonly>
            </div>
            @if ($errors->has('price') || $errors->has('quality'))
                <p class="mt-2 text-sm text-red-500">
                    @error('price') {{ $message }} @enderror
                    @error('quality') {{ $message }} @enderror
                </p>
            @endif
        </div>

        <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Zatwierdź
                </button>
            @else
                <div style="color: red; font-weight: bolder; font-size: larger">
                    Uwaga!!! Masz już przetarg na swoim koncie zawierający jeden z tych produktów jeśli potrzebujesz zmienić swój przetarg zadzwoń pod numer 576 205 389
                </div>
            @endif
            <div id="spinner" class="hidden">
                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </form>
</div>

<script>
    function updateValues() {
        const slider = document.getElementById('price-quality-slider');
        const priceInput = document.getElementById('price-input');
        const qualityInput = document.getElementById('quality-input');

        const priceValue = slider.value;
        const qualityValue = 100 - slider.value;

        priceInput.value = priceValue;
        qualityInput.value = qualityValue;
    }

    const form = document.querySelector('form');
    const spinner = document.getElementById('spinner');

    form.addEventListener('submit', function() {
        spinner.classList.remove('hidden');
    });

    window.addEventListener('load', function() {
        spinner.classList.add('hidden');
    });
</script>
</body>
</html>
