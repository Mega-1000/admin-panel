<div class='row message-row' data-messageid="{{ $message->id }}">
    @if ($message->customer())
        <div class='col-sm-2'>&nbsp;</div>
    @endif
    <div class='col-sm-10'>
        <div class="{{ $message->customer() ? 'text-right alert-warning' : ($message->user() ? 'text-left alert-success' : 'text-left alert-info') }} alert">
            @if ($message->customer())
                <strong>Klient</strong> [{{ $message->created_at }}]:
            @else
                [{{ $message->created_at }}]
                @if ($message->employee())
                    <strong>{{ $message->employee()->firstname }}:</strong>
                @else
                    <strong>{{ $message->user()->name }}:</strong>
                @endif
            @endif
            <br>
            {{ $message->message }}
        </div>
    </div>
</div>