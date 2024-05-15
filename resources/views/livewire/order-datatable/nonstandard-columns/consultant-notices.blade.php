@if(array_key_exists('chat', $data) && !empty($data['chat']) && array_key_exists('messages', $data['chat']))
    @php
        $latestMessages = collect($data['chat']['messages'])->sortByDesc('created_at')->take(3);
    @endphp

    @foreach($latestMessages as $message)
        <hr>
        {{ $message['message'] }} - {{ \Carbon\Carbon::parse(explode('.', $message['created_at'])[0])->addHours(2) }}
        {{ $message['user']['login'] }}
        <br>
        <hr>
    @endforeach
@endif
