@if(array_key_exists('chat', $data) && !empty($data['chat']) && array_key_exists('messages', $data['chat']))
    @foreach($data['chat']['messages'] as $message)
        <hr>
        {{ $message['message'] }} - {{ explode('.', $message['created_at'])[0] }}
        <br>
        <hr>
    @endforeach
@endif
