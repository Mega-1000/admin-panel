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
    <form class="form-group-default m-6" action="{{ route('auctions.store', ['chat' => $chat->id]) }}" method="post">
        @csrf

        <label for="base-input" class="block mb-2 text-sm font-medium mt-2">Data zakończenia przetargu</label>
        <input type="date" id="base-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Data zakończenia przetargu" name="end_of_auction">

        <label for="base-input" class="block mb-2 text-sm font-medium mt-2">Wstępny termin dostawy (Przynajmniej 2 dni robocze od zakończenia przetargu)</label>
        <input type="date" id="base-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Wstępny termin dostawy (Przynajmniej 2 dni robocze od zakończenia przetargu)" name="date_of_delivery">

        <label for="base-input" class="block mb-2 text-sm font-medium mt-2">Podział procentowy cena/jakość</label>

        <input type="text" id="quality-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Cena" value="50" name="price">

        <input type="text" id="price-input" class="mt-4 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Jakość" value="50" name="quality">

        <button class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Zatwierdź
        </button>

        <a href="{{ URL::previous() }}" class="mt-4 bg-red-500 ml-4 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            Anuluj
        </a>
    </form>
</body>
