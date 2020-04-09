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
            <td>{{ $chat->customers()->first()->addresses()->whereNotNull('phone')->first()->phone ?? '' }}</td>
            <td>{{ implode(PHP_EOL, ChatHelper::formatChatUsers($chat->users)) }}</td>
            <td>{{ implode(PHP_EOL, ChatHelper::formatChatUsers($chat->employees)) }}</td>
            <td>{{ '['.$chat->lastMessage->created_at.'] '.$chat->lastMessage->message }}</td>
            <td><a href="{{ $chat->url }}" class="btn btn-large btn-success">Pokaż</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
