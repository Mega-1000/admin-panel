@extends('layouts.datatable')
@section('app-header')
    @include('email_module._header')
@endsection

@section('table')

    <form action="{{ action('EmailSettingsController@update',[$emailSetting->id]) }}" method="POST" class="form-horizontal">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="form-group">
            <div class="col-sm-6">
                <label>Zdarzenie:</label>
                <select class="form-control" id="status" name="status" required>
                    @foreach ($statuses as $name => $label)
                        <option @if($emailSetting->status == $name) selected @endif value="{{ $name }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-4">
                <label>Czas (w minutach):</label>
                <input type="number" class="form-control" id="time" name="time" min="0" value="{{$emailSetting->time}}" />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-10">
                <label>Nazwa ustawienia:</label>
                <input type="text" class="form-control" id="title" name="title" value="{{$emailSetting->title}}" required />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-10">
                @include('email_module/_tags_selector')
                <label>Wiadomość:</label>
                <textarea rows="20" class="form-control" id="content" name="content" required>{{$emailSetting->content}}</textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Zapisz</button>
        <a href="{{ action('EmailSettingsController@index') }}" type="submit" class="btn btn-default">Anuluj</a>
    </form>
@endsection
