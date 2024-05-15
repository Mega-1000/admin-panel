<div class="panel panel-default" style="max-height: calc(100vh - 200px); overflow-y: auto;">
    <div class="panel-body chat-panel" style="border: 2px solid #000; border-radius: 7px;">
        {{ dd($chatMessages) }}
        @if ( isset($chatMessages) )
            @foreach ($chatMessages as $message)
                {{ $message }}
                @include ('chat/single_message', ['message' => $message])
            @endforeach
        @endif
    </div>
</div>
