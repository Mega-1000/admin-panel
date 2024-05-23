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
    </style>
</head>

<body>
    <form class="form-group-default m-6" action="{{ route('auctions.store', ['chat' => $chat->id, 'backUrl' => URL::previous()]) }}" method="post">
        @csrf

        <label for="price-quality-slider" class="block mb-2 text-sm font-medium mt-6">Podział procentowy cena/jakość</label>

        <div class="slider-container">
            <div class="labels">
                <span>Cena</span>
                <span>Jakość</span>
            </div>
            <input type="range" id="price-quality-slider" class="slider" min="0" max="100" value="50" oninput="updateValues()">
        </div>

        <div class="output-container">
            <input type="text" id="price-input" readonly value="50" name="price" placeholder="Cena">
            <input type="text" id="quality-input" readonly value="50" name="quality" placeholder="Jakość">
        </div>


        <label for="base-input" class="block mb-2 text-sm font-medium mt-6">Podział procentowy cena/jakość</label>

        Cena
        <input type="text" id="quality-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Cena" value="50" name="price">

        <div class="mt-4">
            Jakość
            <input type="text" id="price-input" class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Jakość" value="50" name="quality">
        </div>

        <button class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
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
