<div class='row message-row' data-messageid="{{ $message->id }}">
    @if ($message->customer())
        <div class='col-sm-2'>&nbsp;</div>
    @endif
    <div class='col-sm-10'>
        <div class="{{ $message->customer() ? 'text-right alert-warning' : 'text-left alert-info' }} alert">
            @if ($message->employee())
                <strong>{{ $message->employee()->firstname }}:</strong><br>
            @elseif ($message->user())
                <strong>{{ $message->user()->firstname }}:</strong><br>
            @endif
            {{ $message->message }}
        </div>
    </div>
</div>