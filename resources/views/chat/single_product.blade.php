@if(is_a($product, \App\Entities\OrderItem::class))
    <div class="product">
        <img class="image-product" src="{{$product->product->getImageUrl()}}"
             onerror="this.onerror=null;this.src='http://via.placeholder.com/300'"/>
        <p class="product-description">
            {{ $product->product->name }}
            ilość: {{ $product->quantity }}
            cena: {{ $product->price }} PLN
        </p>

        <table class="price-table">
            <tr>
                <th>Jednostka handlowa ({{ $product->product->packing->unit_commercial }})</th>
                <th>Jednostka podstawowa ({{$product->product->packing->unit_basic}})</th>
                <th>Jednostka obliczeniowa ({{$product->product->packing->calculation_unit}})</th>
                <th>Jednostka zbiorcza ({{$product->product->packing->unit_of_collective}})</th>
            </tr>
            <tr>
                <td colspan="4">Cena zakupowa netto</td>
            </tr>
            <tr>
                <td>{{ $product->net_purchase_price_commercial_unit_after_discounts }} PLN</td>
                <td>{{ $product->net_purchase_price_basic_unit_after_discounts }} PLN</td>
                <td>{{ $product->net_purchase_price_calculated_unit_after_discounts }} PLN</td>
                <td>{{ $product->net_purchase_price_aggregate_unit_after_discounts }} PLN</td>
            </tr>
            <tr>
                <td colspan="4">Cena zakupowa brutto</td>
            </tr>
            <tr>
                <td>{{number_format($product->net_purchase_price_commercial_unit_after_discounts * 1.23, 2)}} PLN</td>
                <td>{{number_format($product->net_purchase_price_basic_unit_after_discounts * 1.23, 2)}} PLN</td>
                <td>{{number_format($product->net_purchase_price_calculated_unit_after_discounts * 1.23, 2)}} PLN</td>
                <td>{{number_format($product->net_purchase_price_aggregate_unit_after_discounts * 1.23, 2)}} PLN</td>
            </tr>
        </table>
    </div>
@else
    <div class="product">
        <img width="100" height="100" src="{{$product->getImageUrl()}}"
             onerror="this.onerror=null;this.src='http://via.placeholder.com/300'"/>
        {{ $product->name }}
        cena: {{ $product->price->gross_selling_price_commercial_unit }} PLN / {{ $product->packing->unit_commercial }}
    </div>
@endif
