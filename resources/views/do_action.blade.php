<iframe src="{{ route('orders.edit', $order->id) }}" width="100%" height="1000px" frameborder="0"></iframe>
<iframe src="/auctions/{{ $order->chat->auctions()->first()->id }}/end" width="100%" height="1000px" frameborder="0"></iframe>

// get-bascet
<iframe src="/admin/orders/{{ $order->id }}/get-basket" width="100%" height="1000px" frameborder="0"></iframe>
@php
    $messagesHelper = new App\Helpers\MessagesHelper();
    $order['id'] = $order['id'] ?? 0;
    $messagesHelper->chatId = \App\Entities\Order::find($order['id'])?->chat?->id;
    $token = $messagesHelper->getChatToken($order['id'], auth()->id());
@endphp
<iframe src="/chat/{{ $token }}" width="100%" height="1000px" frameborder="0"></iframe>
