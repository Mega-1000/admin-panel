<div class="panel panel-default" style="max-height: calc(100vh - 200px); overflow-y: auto;">
    <div class="panel-body chat-panel" style="border: 2px solid #000; border-radius: 7px;">
        @if ( isset($chatMessages) )
            @foreach ($chatMessages as $message)
                @if( $userType == MessagesHelper::TYPE_USER || isset($assignedMessagesIds[$message->id] ))
                    @if ($userType != MessagesHelper::TYPE_USER && $message->area != 0)
                        @continue
                    @endif
                    @include ('chat/single_message', ['message' => $message])
                @endif
            @endforeach
        @else
            Brak wiadomości...
        @endif
    </div>
</div>
