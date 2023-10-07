@foreach($items as $item)
    @include('orders.single_item', ['item' => $item->product, 'quantity' => $item->quantity, 'orderItem' => $item])

@endforeach
