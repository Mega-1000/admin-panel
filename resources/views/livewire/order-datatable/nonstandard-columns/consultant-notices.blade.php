@if(array_key_exists('chat', $data) && !empty($data['chat']) && array_key_exists('messages', $data['chat']))
    @php
        $latestMessages = collect($data['chat']['messages'])->sortByDesc('created_at')->take(3);
    @endphp

    @foreach($latestMessages as $message)
        <hr>
        {{ $message['message'] }} - {{ explode('.', $message['created_at'])[0] }}
        <br>
        <hr>
    @endforeach
@endif
