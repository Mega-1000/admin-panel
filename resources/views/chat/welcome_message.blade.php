<div class="row" data-messageid="0" data-user-id="0" data-area="0">
    <div class="col-sm-2">&nbsp;</div>
    <div class="col-sm-12">
        <div class="text-left alert-info alert">
            [{{ $chat->created_at->toDateTimeString() }}]
            <div class="msg-content" style="white-space: pre-line;">
                Witamy!
                Konsultant zapoznaje się ze sprawą wkrótce się odezwie.
                Zajmuje to zwykle do kilku minut.
                @if($chat->questions_tree && $userType === MessagesHelper::TYPE_USER)
                    <br><br>
                    Ścieżka FAQ użytkownika:<br>
                    @foreach (json_decode($chat->questions_tree) as $questionData)
                        -> {{ $questionData->question }}<br>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
