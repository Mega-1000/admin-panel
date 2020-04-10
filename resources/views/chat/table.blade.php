<table id="dataTable" class="table table-hover">
    <thead>
    <tr>
        <th>ID</th>
        <th>Temat</th>
        <th>Telefon klienta</th>
        <th>Dane opiekuna</th>
        <th>Dane konsultanta</th>
        <th>Ostatnia wiadomość</th>
        <th>Przejrzyj/odpowiedz</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($chats as $chat)
        <tr>
            <td>{{ $chat->id }}</td>
            <td>{{ $chat->title }}</td>
            <td>{{ $chat->customers->first()->addresses->first()->phone ?? '' }}</td>
            <td>{!! implode('<br />' , ChatHelper::formatChatUsers($chat->users)) !!}</td>
            <td>{!! implode('<br />' , ChatHelper::formatChatUsers($chat->employees)) !!}</td>
            <td>{{ '['.$chat->lastMessage->created_at.'] '.$chat->lastMessage->message }}</td>
            <td>
                <a href="{{ $chat->url }}" target="_blank" class="btn btn-large btn-success go_to_chat">Pokaż</a>
                @if ($chat->has_new_message)
                    <i style="color: red" class="fas fa-comment new_messege"></i>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@section('javascript')
    <script>
        $('.go_to_chat').click((event) =>
            $($(event.target).parent().find('.new_messege').hide()));
    </script>
    @parent
@endsection
