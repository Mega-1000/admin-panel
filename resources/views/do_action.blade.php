<iframe src="{{ route('orders.edit', ['order' => $order->id]) }}" width="100%" height="1000px" frameborder="0"></iframe>
<iframe src="/auctions/{{ $order->chat->auction->id }}/end" width="100%" height="1000px" frameborder="0"></iframe>
