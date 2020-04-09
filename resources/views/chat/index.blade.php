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
    @include('chat.table', ['chats' => $chats])

@endsection
@section('javascript')
    <script type="text/javascript" src="{{ URL::asset('js/helpers/data-tables.js') }}"></script>
@endsection
