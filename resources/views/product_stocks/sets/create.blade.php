@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('sets.edit')
    </h1>
@endsection

@section('table')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ action('SetsController@store') }}" method="POST">
        {{ csrf_field() }}
        <div class="product_stocks-general" id="general">
            <div class="form-group">
                <label for="name">@lang('sets.form.packet_name')</label>
                <input type="text" class="form-control" id="name" name="name">
            </div>
            <div class="form-group">
                <label for="number">@lang('sets.form.number')</label>
                <input type="text" class="form-control" id="number" name="number">
            </div>
        </div>
        <button type="submit" id="store__packet" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
