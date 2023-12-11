@if(array_key_exists('chat', $order) && array_key_exists('messages', $order['chat']))
    @foreach($order['chat']['messages'] as $message)
        <hr>
        {{ $message['message'] }}
        <br>
        <hr>
    @endforeach
@endif
