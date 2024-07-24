// iframe to order edit
<iframe src="{{ route('order.edit', ['order' => $order->id]) }}" width="100%" height="1000px" frameborder="0"></iframe>
// iframe to auction table
<iframe src="/auctions/{{ $order->chat->auction->id }}/end" width="100%" height="1000px" frameborder="0"></iframe>
