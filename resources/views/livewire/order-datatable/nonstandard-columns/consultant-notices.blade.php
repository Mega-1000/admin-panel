@if(array_key_exists('chat', $data) && !empty($data['chat']) && array_key_exists('messages', $data['chat']))
    @php
        $latestMessages = collect($data['chat']['messages'])->sortByDesc('created_at')->take(3);
    @endphp

    @foreach($latestMessages as $message)
        <hr>
        @php
            $m = \App\Entities\Message::find($message['id']);
            $userType = '';
            if ($m->user()) {
                $userType = 'Konsultant';
            } elseif ($m->customer()) {
                $userType = 'Klient';
            } elseif ($m->warehouse()) {
                $userType = 'Magazyn';
            }
        @endphp
        {{ $message['message'] }} - {{ \Carbon\Carbon::parse(explode('.', $message['created_at'])[0])->addHours(2) }}
        {{ $userType }}
        <br>
        <hr>
    @endforeach
@endif
