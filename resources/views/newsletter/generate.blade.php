<script src="https://cdn.tailwindcss.com" ></script>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
    @foreach($products as $product)
        @php($product = \App\Entities\Product::where('symbol', $product->product)->first())
        @if($product)
            @php($newsletter = \App\Entities\Newsletter::where('product', $product->symbol)->first())
            <div class="p-4 border rounded-lg shadow-lg">
                <img src="{{ $product?->url_for_website }}" class="w-full h-48 object-cover rounded-lg">
                <div class="mt-4">
                    <div class="text-2xl font-semibold">
                        Cena: {{ $product->price->allegro_gross_selling_price_after_all_additional_costs }} zł
                    </div>
                    <h3 class="text-lg sm:text-xl md:text-2xl font-semibold">{{ $product?->name }}</h3>

                    <p class="text-gray-600">{!! nl2br($newsletter?->description) !!}</p>

                    <p class="text-gray-600">{{ $product?->symbol }}</p>

                    <a href="{{ $newsletter->auction_url }}" target="_blank" class="px-4 py-2 bg-blue-500 rounded text-white mt-4">
                        Zobacz aukcję
                    </a>
                </div>
            </div>
        @endif
    @endforeach
</div>
