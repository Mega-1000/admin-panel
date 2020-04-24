@if(is_a($product, \App\Entities\OrderItem::class))
    <div class="product">
        <img width="100" height="100" src="{{$product->product->getImageUrl()}}"
             onerror="this.onerror=null;this.src='http://via.placeholder.com/300'"/>
        {{ $product->product->name }}
        ilość: {{ $product->quantity }}
        cena: {{ $product->price }} PLN
    </div>
@else
    <div class="product">
        <img width="100" height="100" src="{{$product->getImageUrl()}}"
             onerror="this.onerror=null;this.src='http://via.placeholder.com/300'"/>
        {{ $product->name }}
        cena: {{ $product->price->gross_selling_price_commercial_unit }} PLN
    </div>
@endif
