<div class='row message-row' data-messageid="{{ $message->id }}">
    @if ($message->customer())
        <div class='col-sm-2'>&nbsp;</div>
    @endif
    <div class='col-sm-10'>
        <div
            class="{{ $message->customer() ? 'text-right alert-warning' : ($message->user() ? 'text-left alert-success' : 'text-left alert-info') }} alert">
            @if ($message->customer())
                <strong>Klient
                    {{ '<' . $message->customer()->login . '>' }}
                    @if ( $message->customer()->addresses()->whereNotNull('phone')->count() > 0)
                        tel: {{ $message->customer()->addresses()->whereNotNull('phone')->first()->phone }}
                    @endif
                </strong>
                [{{ $message->created_at }}]:
            @else
                [{{ $message->created_at }}]
                @if ($message->employee())
                    <strong>
                        {{ $message->employee()->firstname . ' ' . $message->employee()->lastname }}
                            @if(isset($message->employee()->email))
                                {{ '<'. $message->employee()->email . '>' }}
                            @endif
                            @if(isset($message->employee()->phone))
                                {{ 'tel: '. $message->employee()->phone }}
                            @endif
                        @if($message->employee()->employeeRoles->count() > 0)
                            <br />
                                ({{
                                    implode(', ' , $message->employee()->employeeRoles->map(function ($role) {
                                        return $role->name;
                                    })->toArray())

                                }}):
                        @endif
                    </strong>
                @else
                    <strong>
                        nr {{ $message->user()->name }}
                        {{ $message->user()->fistname }}
                        {{ $message->user()->lastname }}
                        @if($message->user()->email)
                            {{ '<' . $message->user()->email . '>' }}
                        @endif
                        @if($message->user()->phone)
                            tel: {{ $message->user()->phone }}:
                        @else
                            :
                        @endif
                        </strong>
                @endif
            @endif
            <br>
            {{ $message->message }}
        </div>
    </div>
</div>
