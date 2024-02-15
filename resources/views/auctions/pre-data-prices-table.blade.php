
<table>
    <thead>
    <tr>
        <th>
            <h5 style="text-align: right">
                Ceny za m3
            </h5>
        </th>
        @foreach($order->items->pluck('product') as $product)
            <th>
                @php
                    $name = $product->name;
                    $words = explode(' ', $name);
                    array_shift($words);
                    $name = implode(' ', $words);
                @endphp
                {{ $name }}
                <button class="btn btn-primary">
                    Sortuj
                </button>
            </th>
        @endforeach
    </tr>
    </thead>
    <tbody>

    @php
        $displayedFirmSymbols = [];
    @endphp

    @foreach($firms as $firm)
        @php
            $symbol = $firm->symbol; // Assuming $firm->firm->symbol gives you the symbol you want to display
        @endphp

        <tr>
            <td>
                {{ $symbol }}
            </td>

            @php
                $prices = [];
                $items = $order->items->pluck('product')->toArray();

                foreach ($items as $item) {
                    $variation = App\Entities\Product::where('product_group', $item['product_group'])->where('product_name_supplier', $firm->symbol)->first();

                    $prices[] = $variation?->price->gross_purchase_price_basic_unit_after_discounts;
                }
            @endphp

            @foreach($prices as $price)
                <td>
                    {{ $price }} zł
                </td>
            @endforeach
        </tr>
        @php
            $displayedFirmSymbols[] = $symbol; // Add the symbol to the array so it won't be displayed again
        @endphp
    @endforeach

    </tbody>
</table>
