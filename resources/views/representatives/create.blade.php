<link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">

<form action="{{ route('store-represents', $firm->id) }}" method="POST" class="space-y-6 w-1/2 mx-auto">
    <h1 class="mb-4 font-bold text-3xl">
        Prosimy o podanie danych do przedstawicieli którzy będą oferowali państwa produkty w najniższych cenach. Jeśli nie mają Państwo 5 takich prosimy zostawić pola puste.
    </h1>

    @csrf
    <div id="products" class="grid grid-cols-1 gap-6">
        @for ($i = 0; $i < 5; $i++)
            <div class="product-group bg-white shadow-md rounded-lg p-6">
                <div class="form-group">
                    <label for="products[{{ $i }}][contact_info]" class="block font-medium mb-2">Informacje kontaktowe do przedstawiciela {{ $i }}</label>
                    <input type="text" name="products[{{ $i }}][contact_info]" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>
        @endfor
    </div>

    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition-colors duration-300">Zapisz</button>
</form>
