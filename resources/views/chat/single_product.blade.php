@if(is_a($product, \App\Entities\OrderItem::class))
    <div class="product">
        <img class="image-product" src="{{$product->product->getImageUrl()}}"
             onerror="this.onerror=null;this.src='http://via.placeholder.com/300'"/>
        <div class="product-description">
            <p>
                {{ $product->product->name }}
            </p>
            <p>
                symbol: {{ $product->product->symbol }}
            </p>
            <p>
                ilość: {{ $product->quantity }}
            </p>
            <p>
                cena: {{ $product->price }} PLN
            </p>
        </div>
        @if($user_type != MessagesHelper::TYPE_CUSTOMER)
            <form style="display: flex; flex-direction: column" action="{{ $routeForEditPrices }}" method="POST">
                <input type="hidden" class="unit_consumption"
                       value="{{ $product->product->packing->unit_consumption }}">
                <input type="hidden" class="number_of_sale_units_in_the_pack"
                       value="{{ $product->product->packing->number_of_sale_units_in_the_pack }}">
                <input type="hidden" class="numbers_of_basic_commercial_units_in_pack"
                       value="{{ $product->product->packing->numbers_of_basic_commercial_units_in_pack }}">
                <input type="hidden" name="item_id" value="{{ $product->id }}">
                @include('chat/pricing_table')
                <input type="submit" value="aktualizuj">
            </form>
        @endif
    </div>
@else
    <div class="product">
        <img width="100" height="100" src="{{$product->getImageUrl()}}"
             onerror="this.onerror=null;this.src='http://via.placeholder.com/300'"/>
        {{ $product->name }}
        cena: {{ $product->price->gross_selling_price_commercial_unit }} PLN / {{ $product->packing->unit_commercial }}
    </div>
@endif
