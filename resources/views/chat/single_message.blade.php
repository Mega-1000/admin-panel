<div class='row '>
    @php
        $header = ChatHelper::getMessageHeader($message);
    @endphp
    @if ($message->customer())
        <div class='col-sm-2'>&nbsp;</div>
    @endif
    <div class='{{ $message->user() ? 'col-sm-12' : 'col-sm-10' }}'>
        <div
            class="{{ $message->customer() ? 'text-right alert-warning' : ($message->user() ? 'text-left bg-primary' : 'text-left alert-info') }} alert"
            {{ $message->user() ? 'style="background-color: green"' : '' }}>
            <strong>{{ $message->customer() ? '' : ($message->user() ? '' : 'Wiadomość systemowa') }}</strong>
            @if ($message->customer())
                <strong> {!! $header !!} </strong> [{{ $message->created_at }}]
            @else
                [{{ $message->created_at }}]
                <strong> {!! $header !!} </strong>
            @endif
            <div class="msg-content" style="white-space: pre-line;">
                {{ $message->message }}
            </div>
            @if ($message->attachment_path)
                <a class="attachment-path" style="display: block; margin-top: 10px; color: #000;"
                    href="{{ asset('storage/' . $message->attachment_path) }}" download="{{ $message->attachment_name }}">
                    załącznik: {{ $message->attachment_name }}
                </a>
            @endif
        </div>
    </div>
</div>
