@foreach($orders as $order)
    {{ $order->id }}

    @if($order->labels->contains('id', 77))
        Awizacja nie potwierdzona
    @endif

@endforeach
