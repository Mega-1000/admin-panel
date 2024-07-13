<button wire:click="setOrderToMove({{ $order['id'] }})" class="btn btn-secondary w-100">
    Rozpocznij przenoszenie
</button>

<button wire:click="moveDataToOrder({{ $order['id'] }})" class="btn btn-primary w-100" id="finalize-order-moving">
    Zakończ przenoszenie
</button>

<a class="btn btn-sm btn-primary" href="{{ route('orders.edit', $order['id']) }}?page={{ request()->query('page') }}">
    Edytuj
</a>

<form id="deleteForm" action="/admin/orders/{{$order['id']}}" method="POST">
    @method('delete')
    @csrf
    <button class="btn btn-danger btn-sm" id="deleteBtn">Usuń</button>
</form>

{{--<a href="/admin/orderReturn/{{ $order['id'] }}" class="btn btn-sm btn-danger">--}}
{{--    <i class="glyphicon glyphicon-share-alt"></i>--}}
{{--    <span class="hidden-xs hidden-sm">Zwrot</span>--}}
{{--</a>--}}
{{--<br>--}}

@php
    $order = App\Entities\Order::find($order['id']);
@endphp

@if(!is_array($order) && $order?->chat?->auctions?->count() > 0)
    <a target="__blank" href="/auctions/{{ $order->chat->auctions->first()->id }}/end" class="btn btn-sm btn-success">
        <span class="hidden-xs hidden-sm">Zobacz tabelę aukcji</span>
    </a>
@endif

@php
    $messagesHelper = new App\Helpers\MessagesHelper();
    $order['id'] = $order['id'] ?? 0;
    $messagesHelper->chatId = \App\Entities\Order::find($order['id'])?->chat?->id;
    $token = $messagesHelper->getChatToken($order['id'], auth()->id());
@endphp
<br>
<a href="/chat/{{ $token }}" target="_blank" class="btn btn-sm btn-primary">
    <span class="hidden-xs hidden-sm">Chat</span>
</a>

<a href="{{ route('createAvisation', $order['id'])}}" class="btn btn-sm btn-primary">
    <span class="hidden-xs hidden-sm">
        Szybka awizacja
    </span>
</a>

<a href="https://admin.mega1000.pl/order/{{ $order->id }}/getMails" class="btn btn-primary">
    Raport wiadomości
</a>

<a target="_blank" class="btn btn-sm btn-primary" href="/admin/create-package-product-order/${id}">Stwórz produkt pakowy</a>

{{--@if ($order['is_buying_admin_side'])--}}
    <a class="btn btn-primary" href="/admin/accept-products/{{ $order['id'] }}" target="__blank">Przyjmij na stany magazynowe</a>
{{--@endif--}}

