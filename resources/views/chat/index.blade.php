@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> Czaty
    </h1>
@endsection
@section('table')
    @if (!$showAll)
        <div class='alert alert-warning'>
            Wyświetlane są tylko czaty, w których ostatnia wiadomość została napisana nie wcześniej niż 30 dni temu.<br>
            Aby wyświetlić wszystkie czaty kliknij <a href="{{ route('chat.index', ['all' => 1]) }}">TUTAJ</a>.
        </div>
    @endif
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

@endsection
@section('javascript')
    <script type="text/javascript" src="{{ URL::asset('js/helpers/data-tables.js') }}"></script>
@endsection
