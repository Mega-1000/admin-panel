<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Delivery Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <form action="{{ route('shippingTodayStore', $order->id) }}" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" style="width: 40%; margin: 40px auto 0;">
        @csrf
        <h2 class="text-xl font-bold mb-4">Czas dostawy zamówienia</h2>
        <div class="flex items-center justify-between mb-4">
            <div class="relative">
                <label for="time_from" class="block text-gray-700 font-bold mb-2">Od:</label>
                <input name="time_from" required type="time" id="time_from" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="relative">
                <label for="time_to" class="block text-gray-700 font-bold mb-2">Do:</label>
                <input name="time_to" required type="time" id="time_to" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
        </div>
        <div class="mb-6">
            <label for="driver_phone" class="block text-gray-700 font-bold mb-2">Numer telefonu do kierowcy:</label>
            <input type="text" name="driver_phone" id="driver_phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Zatwierdź
            </button>
        </div>
    </form>
</body>
