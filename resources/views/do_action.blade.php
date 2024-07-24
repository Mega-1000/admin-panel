<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive iframes</title>
    <style>
        .iframe-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .iframe-wrapper {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            height: 0;
            overflow: hidden;
        }
        .iframe-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        .expanded {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1000;
        }
    </style>
</head>
<body>
<div class="iframe-container">
    <div class="iframe-wrapper">
        <iframe src="{{ route('orders.edit', $order->id) }}"></iframe>
    </div>
    <div class="iframe-wrapper">
        <iframe src="/auctions/{{ $order->chat->auctions()->first()->id }}/end"></iframe>
    </div>
    <div class="iframe-wrapper">
        <iframe src="/admin/orders/{{ $order->id }}/get-basket"></iframe>
    </div>
    @php
        $messagesHelper = new App\Helpers\MessagesHelper();
        $order['id'] = $order['id'] ?? 0;
        $messagesHelper->chatId = \App\Entities\Order::find($order['id'])?->chat?->id;
        $token = $messagesHelper->getChatToken($order['id'], auth()->id());
    @endphp
    <div class="iframe-wrapper">
        <iframe src="/chat/{{ $token }}"></iframe>
    </div>
</div>

<script>
    document.querySelectorAll('.iframe-wrapper').forEach(wrapper => {
        wrapper.addEventListener('dblclick', () => {
            wrapper.classList.toggle('expanded');
        });
    });
</script>
</body>
</html>
