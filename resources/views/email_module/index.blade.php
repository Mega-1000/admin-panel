@extends('layouts.datatable')
@section('app-header')
    @include('email_module._header')
@endsection

@section('table')


    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Status</th>
                <th>Tytuł</th>
                <th>Czas</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($emailSettings as $s)

            <tr data-toggle="tooltip" data-placement="top" data-html="true" title="{{ \Illuminate\Support\Str::limit($s->content, 200, '...')}}">
                <td>{{$s->id}}</td>
                <td>{{$s->statusTitle}}</td>
                <td>{{$s->title}}</td>
                <td>{{$s->time}} min.</td>
                <td>
                    <form method="post" action="{{ action('EmailSettingsController@destroy',[$s->id]) }}" onsubmit="return confirm('Czy na pewno chcesz usunąć rekord?');">
                        @method('delete')
                        @csrf
                        <a style="text-decoration: none;" href="{{ action('EmailSettingsController@edit',[$s->id]) }}" class="btn btn-warning btn-xs"><span>Edytuj</span></a>
                        <button type="submit" class="btn btn-danger btn-sm">Usuń</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection