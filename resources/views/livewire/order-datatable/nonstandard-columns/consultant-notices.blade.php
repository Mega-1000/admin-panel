@foreach($order['chat']['messages'] as $message)
    <hr>
    {{ $message['message'] }}
    <br>
    <hr>
@endforeach
