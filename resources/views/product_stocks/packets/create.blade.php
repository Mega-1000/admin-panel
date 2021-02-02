@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stock_packets.edit')
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
    <form action="{{ action('ProductStockPacketsController@store') }}" method="POST">
        {{ csrf_field() }}
        <div class="product_stocks-general" id="general">
            <div class="form-group">
                <label for="packet_name">@lang('product_stocks.form.packet_name')</label>
                <input type="text" class="form-control" id="packet_name" name="packet_name">
            </div>
            <div class="form-group">
                <label for="packet_quantity">@lang('product_stocks.form.packet_quantity')</label>
                <input type="number" class="form-control" id="packet_quantity" name="packet_quantity">
            </div>
            <div id="product__packet--form">
                <h3>@lang('product_stock_packets.form.product_list')</h3>
                <div id="products__list">

                </div>
                <h3>@lang('product_stock_packets.form.add_product_header')</h3>
                <select style="margin-left: 10px;" class="form-control text-uppercase selectpicker" data-live-search="true" id="product__select" name="product_id">
                    <option value="" selected="selected">@lang('product_stock_packets.form.choose_product')</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                <label for="packet_product_quantity">@lang('product_stocks.form.packet_product_quantity')</label>
                <input type="text" class="form-control" id="packet_product_quantity" name="packet_product_quantity">
                <button id="product__assign" class="btn btn-success">@lang('product_stocks.form.buttons.add_product')</button>
            </div>
            </div>
        <button type="submit" id="store__packet" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@include('product_stocks.packets.modals.formModals')
@include('product_stocks.packets.includes.scripts')
