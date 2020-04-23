<div class='row message-row' data-messageid="{{ $message->id }}">
    @if ($message->customer())
        <div class='col-sm-2'>&nbsp;</div>
    @endif
    <div class='{{ $message->user() ? 'col-sm-12' : 'col-sm-10'}}'>
        <div
            class="{{ $message->customer() ? 'text-right alert-warning' : ($message->user() ? 'text-left bg-primary' : 'text-left alert-info') }} alert">
            @if ($message->customer())
                <strong> {!! $header !!} </strong> [{{ $message->created_at }}]
            @else
                [{{ $message->created_at }}]
                    <strong> {!! $header !!} </strong>
            @endif
            <br>
            {{ $message->message }}
        </div>
    </div>
</div>
