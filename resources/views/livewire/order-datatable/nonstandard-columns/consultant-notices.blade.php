@if(array_key_exists('chat', $data))
    @foreach($data['chat']['messages'] as $message)
        <hr>
        {{ $message['message'] }}
        <br>
        <hr>
    @endforeach
@endif
