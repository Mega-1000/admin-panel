@if(array_key_exists('chat', $data) && !empty($data['chat']) && array_key_exists('messages', $data['chat']))
    @foreach($data['chat']['messages'] as $message)
        <hr>
        {{ $message['message'] }} - {{ $message['created_at'] }}
        <br>
        <hr>
    @endforeach
@endif
