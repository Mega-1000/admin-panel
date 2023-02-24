<div class="panel panel-default" style="max-height: calc(100vh - 200px); overflow-y: scroll">
    <div class="panel-body">
        @foreach ($chat->messages as $message)
            @if( $userType == MessagesHelper::TYPE_USER || isset($assignedMessagesIds[$message->id] ))
                @include ('chat/single_message', ['message' => $message])
            @endif
        @endforeach
    </div>
</div>
