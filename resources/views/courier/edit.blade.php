@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-list"></i> Lista kurierów
    </h1>
@endsection

@section('table')

    <form action="{{ action('CourierController@update',[$courier->id]) }}" method="POST" class="form-horizontal">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="form-group">
            <div class="col-sm-6">
                <label>Nazwa:</label>
                <input type="text" class="form-control" id="courier_name" name="courier_name" value="{{$courier->courier_name}}" />
            </div>
            <div class="col-sm-6">
                <label>Klucz:</label>
                <input type="text" class="form-control" id="courier_key" name="courier_key" value="{{$courier->courier_key}}" />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-2">
                <label>Pozycja:</label>
                <input type="number" class="form-control" id="item_number" name="item_number" value="{{$courier->item_number}}" required />
            </div>
            <div class="col-sm-2">
                    <label for="active">Aktywny:</label>
                <div class="form-check">
                    <input type="hidden" name="active" value="0" />
                    <input type="checkbox" class="form-check-input" id="active" name="active" value="1" @if($courier->active==1) checked @endif />
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Zapisz</button>
        <a href="{{ action('CourierController@index') }}" type="submit" class="btn btn-default">Anuluj</a>
    </form>
@endsection
