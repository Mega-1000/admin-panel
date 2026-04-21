@extends('layouts.datatable')
@section('app-header')
    @include('email_module._header')
@endsection

@section('table')
    <!-- errors -->
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Ups!</strong> Wystąpiły problemy z twoim wpisem.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}<br></li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ action('EmailSettingsController@store') }}" method="POST" class="form-horizontal">
        {{ csrf_field() }}

        <div class="form-group">
            <div class="col-sm-6">
                <label>Zdarzenie:</label>
                <select class="form-control" id="status" name="status" required>
                    @foreach ($statuses as $name => $label)
                        <option value="{{ $name }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-4">
                <label>Czas (w minutach):</label>
                <input type="number" class="form-control" id="time" name="time" min="0" value="0" />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-10">
                <label>Nazwa ustawienia:</label>
                <input type="text" class="form-control" id="title" name="title" required />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-10">
                @include('email_module/_tags_selector')
                <label>Wiadomość:</label>
                <textarea rows="20" class="form-control" id="content" name="content" required></textarea>
            </div>
        </div>
        <div>
            <!-- is allegro -->
            <div class="form-group">
                <div class="col-sm-10">
                    <label>Wysyłaj do allegro:</label>
                    <input type="checkbox" id="is_allegro" name="is_allegro" />
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Zapisz</button>
        <a href="{{ action('EmailSettingsController@index') }}" type="submit" class="btn btn-default">Anuluj</a>
    </form>
@endsection
