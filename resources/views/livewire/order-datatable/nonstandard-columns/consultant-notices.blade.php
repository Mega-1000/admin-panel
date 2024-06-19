<style>
    .message-container {
        position: relative;
        display: inline-block;
    }

    .message-preview {
        cursor: pointer;
    }

    .message-full {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        padding: 12px 16px;
        z-index: 1;
    }

    .message-container:hover .message-full {
        display: block;
    }
</style>

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
            } elseif ($m->employee()) {
                $userType = 'Magazyn';
            }
            $messageText = $message['message'];
            $firstFiveWords = implode(' ', array_slice(explode(' ', $messageText), 0, 5));
        @endphp
        <div class="message-container">
            <span class="message-preview">{{ $firstFiveWords }}...</span>
            <span class="message-full">{{ $messageText }}</span>
        </div>
        - {{ \Carbon\Carbon::parse(explode('.', $message['created_at'])[0])->addHours(2) }}
        {{ $userType }}
        <br>
        <hr>
    @endforeach
@endif
