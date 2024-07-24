<iframe src="{{ route('orders.edit', $order->id) }}" width="100%" height="1000px" frameborder="0"></iframe>
<iframe src="/auctions/{{ $order->chat->auctions()->first()->id }}/end" width="100%" height="1000px" frameborder="0"></iframe>
