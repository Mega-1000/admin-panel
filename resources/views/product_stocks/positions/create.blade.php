@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-eye"></i> @lang('product_stock_positions.create')
        <a style="margin-left: 15px;" href="{{ action('ProductStocksController@edit', ['id' => $id]) }}"
           class="btn btn-info install pull-right">
            <span>@lang('product_stock_positions.list')</span>
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
    <form action="{{ action('ProductStockPositionsController@store', ['id' => $id]) }}" method="POST">
        {{ csrf_field() }}
        <div class="product_stock_positions-general" id="general">
            <div class="form-group">
                <label for="lane">@lang('product_stock_positions.form.lane')</label>
                <input type="text" class="form-control" id="lane" name="lane"
                       value="{{ old('lane') }}">
            </div>
            <div class="form-group">
                <label for="bookstand">@lang('product_stock_positions.form.bookstand')</label>
                <input type="text" class="form-control" id="bookstand" name="bookstand"
                       value="{{ old('bookstand') }}">
            </div>
            <div class="form-group">
                <label for="shelf">@lang('product_stock_positions.form.shelf')</label>
                <input type="text" class="form-control" id="shelf" name="shelf"
                       value="{{ old('shelf') }}">
            </div>
            <div class="form-group">
                <label for="position">@lang('product_stock_positions.form.position')</label>
                <input type="text" class="form-control" id="position" name="position"
                       value="{{ old('position') }}">
            </div>
            <div class="form-group">
                <label for="position_quantity">@lang('product_stock_positions.form.position_quantity')</label>
                <input type="number" class="form-control" id="position_quantity" name="position_quantity"
                       value="{{ old('position_quantity') }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@section('scripts')
    <script>
        var breadcrumb = $('.breadcrumb:nth-child(2)');
        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/product/stocks'>Stany magazynowe</a></li>");
        breadcrumb.append("<li class='disable'><a href='/admin/product/stocks/{{$id}}/edit'>Edytuj</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$id}}/edit#positions'>Pozycje magazynowe</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Dodaj</a></li>");
    </script>
@endsection