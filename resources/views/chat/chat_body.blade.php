<div class="panel panel-default" style="max-height: calc(100vh - 200px); overflow-y: scroll">
    <div class="panel-body">
        @foreach ($chat->messages as $message)
            @php
                $header = ChatHelper::getMessageHelper($message);
            @endphp
            @include ('chat/single_message', ['message' => $message, 'header' => $header])
        @endforeach
    </div>
</div>
