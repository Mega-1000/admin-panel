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
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .slider-container {
            margin-top: 20px;
        }
        .labels {
            display: flex;
            justify-content: space-between;
        }
        .labels span {
            font-size: 14px;
        }
        .output-container {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .output-container input {
            width: 45%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .slider {
            width: 100%;
            margin: 10px 0;
        }
        .invalid-feedback {
            color: red;
            font-size: 0.875em;
        }
    </style>
</head>

<body>
<form class="form-group-default m-6" action="{{ route('auctions.store', ['chat' => $chat->id, 'backUrl' => URL::previous()]) }}" method="post">
    @csrf

    <label for="end_of_auction" class="block mb-2 text-sm font-medium mt-2">Data zakończenia przetargu</label>
    <input type="datetime-local" id="end_of_auction" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('end_of_auction') is-invalid @enderror" name="end_of_auction" value="{{ old('end_of_auction', now()->setTime(15, 00)) }}">
    @error('end_of_auction')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror

    <label for="notes" class="block mb-2 text-sm font-medium mt-2">Dodatkowe informację do wzięcia pod uwagę przez firmy uczestniczące w przetargu</label>
    <textarea
        id="notes"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('notes') is-invalid @enderror"
        name="notes"
    >{{ old('notes') }}</textarea>
    @error('notes')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror

    <label for="price-quality-slider" class="block mb-2 text-sm font-medium mt-6">Podział procentowy cena/jakość (przesuń suwak aby zmienić ten parametr)</label>

    <div class="slider-container">
        <div class="labels">
            <span>Cena</span>
            <span>Jakość</span>
        </div>
        <input type="range" id="price-quality-slider" class="slider @error('price') is-invalid @enderror @error('quality') is-invalid @enderror" min="0" max="100" value="{{ old('price', 50) }}" oninput="updateValues()">
    </div>

    <div class="output-container">
        <input type="text" id="price-input" value="{{ old('price', 50) }}" name="price" placeholder="Cena" readonly>
        <input type="text" id="quality-input" value="{{ old('quality', 50) }}" name="quality" placeholder="Jakość" readonly>
    </div>
    @if ($errors->has('price') || $errors->has('quality'))
        <div class="invalid-feedback">
            @error('price') {{ $message }} @enderror
            @error('quality') {{ $message }} @enderror
        </div>
    @endif

    <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Zatwierdź
    </button>

    <a href="{{ URL::previous() }}" class="mt-4 bg-red-500 ml-4 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
        Anuluj
    </a>
</form>

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
</script>
</body>
</html>
