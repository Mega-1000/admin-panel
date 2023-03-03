@extends('layouts.datatable')
@section('app-header')
    @include('email_module._header')
@endsection

@section('table')

    <form action="{{ action('EmailSettingsController@store') }}" method="POST" class="form-horizontal">
        {{ csrf_field() }}
        
        <div class="form-group">
            <div class="col-sm-6">
                <label>Zdarzenie:</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="NEW">{{$status['NEW']}}</option>
                    <option value="PRODUCED">{{$status['PRODUCED']}}</option>
                    <option value="PICKED_UP">{{$status['PICKED_UP']}}</option>
                    <option value="PROVIDED">{{$status['PROVIDED']}}</option>
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
                <label>Tytuł:</label>
                <input type="text" class="form-control" id="title" name="title" required />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-10">
                <label>Tytuł:</label>
                <textarea rows="20" class="form-control" id="content" name="content" required></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Zapisz</button>
        <a href="{{ action('EmailSettingsController@index') }}" type="submit" class="btn btn-default">Anuluj</a>
    </form>
@endsection