@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('sets.packet_list')
        <a style="margin-left: 15px;" href="{{ action('ProductStocksController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('product_stocks.list')</span>
        </a>
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
    <div id="setsList"></div>
@endsection
