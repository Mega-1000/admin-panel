@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-eye"></i> @lang('product_stock_positions.edit')
        <a style="margin-left: 15px;" href="{{ route('product_stocks.edit', ['id' => $id]) }}"
           class="btn btn-info install pull-right">
            <span>@lang('firms.back_to_edit')</span>
        </a>
    </h1>
@endsection

@section('table')
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ action('ProductStockPositionsController@update', ['id' => $id, 'position_id' => $productStockPosition->id])}}"
          method="POST">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="panel-body">
            <div class="product_stock_positions-general" id="general">
                <div class="form-group">
                    <label for="lane">@lang('product_stock_positions.form.lane')</label>
                    <input type="text" class="form-control" id="lane" name="lane"
                           value="{{ $productStockPosition->lane }}">
                </div>
                <div class="form-group">
                    <label for="bookstand">@lang('product_stock_positions.form.bookstand')</label>
                    <input type="text" class="form-control" id="bookstand" name="bookstand"
                           value="{{ $productStockPosition->bookstand }}">
                </div>
                <div class="form-group">
                    <label for="shelf">@lang('product_stock_positions.form.shelf')</label>
                    <input type="text" class="form-control" id="shelf" name="shelf"
                           value="{{ $productStockPosition->shelf }}">
                </div>
                <div class="form-group">
                    <label for="position">@lang('product_stock_positions.form.position')</label>
                    <input type="text" class="form-control" id="position" name="position"
                           value="{{ $productStockPosition->position }}">
                </div>
                <div class="form-group">
                    <label for="position_quantity">@lang('product_stock_positions.form.position_quantity')</label>
                    <input type="number" class="form-control" id="position_quantity" name="position_quantity"
                           value="{{ $productStockPosition->position_quantity }}">
                    <input type="hidden" class="form-control" id="different" name="different"
                           value="">
                </div>
                <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            var breadcrumb = $('.breadcrumb:nth-child(2)');

            breadcrumb.children().remove();
            breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
            breadcrumb.append("<li class='active'><a href='/admin/product/stocks'>Stany magazynowe</a></li>");
            breadcrumb.append("<li class='disable'><a href='/admin/product/stocks/{{$id}}/edit'>Edytuj</a></li>");
            breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$id}}/edit#positions'>Pozycje magazynowe</a></li>");
            breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
            var quantity = $('#position_quantity').val();
            $('#position_quantity').on('change', function () {
                var quantityNow = $('#position_quantity').val();
                var different;
                quantity = parseInt(quantity);
                quantityNow = parseInt(quantityNow);
                if ($('#general > div:nth-child(5) > label > span') !== undefined) {
                    $('#general > div:nth-child(5) > label > span').remove();
                }
                if (quantity > quantityNow) {
                    different = parseFloat(quantity) - parseFloat(quantityNow);
                    $('#general > div:nth-child(5) > label').append('<span style="color:red"> -' + different + '</span>');
                    $('#different').val('-' + different);
                } else if (quantity < quantityNow) {
                    different = quantityNow - quantity;
                    $('#general > div:nth-child(5) > label').append('<span style="color:green"> +' + different + '</span>');
                    $('#different').val('+' + different);
                } else if (quantityNow === quantity) {
                    different = 0;
                    $('#general > div:nth-child(5) > label > span').remove();
                    $('#different').val(different);
                }
            });
        });
    </script>
@endsection
