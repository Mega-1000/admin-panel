@if(array_key_exists('chat', $data) && array_key_exists('messages', $data['chat']))
    @foreach($data['chat']['messages'] as $message)
        <hr>
        {{ $message['message'] }}
        <br>
        <hr>
    @endforeach
@endif
