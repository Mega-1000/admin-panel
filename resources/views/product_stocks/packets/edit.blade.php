@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stock_packets.edit')
        <a style="margin-left: 15px;" href="{{ route('product_stock_packets.index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('product_stock_packets.packet_list')</span>
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
    <form method="POST">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
        <div class="product_stocks-general" id="general">
            <div class="form-group">
                <label for="packet_name">@lang('product_stocks.form.packet_name')</label>
                <input type="text" class="form-control" id="packet_name" name="packet_name"
                        value="{{ $productStockPacket->packet_name }}">
            </div>
            <div class="form-group">
                <label for="packet_quantity">@lang('product_stocks.form.packet_quantity')</label>
                <input type="text" class="form-control" id="packet_quantity" name="packet_quantity"
                       value="{{ $productStockPacket->packet_quantity }}">
            </div>
            <h3>@lang('product_stock_packets.form.product_list')</h3>
            @foreach($productStockPacket->items as $productStockPacketItem)
                <div class="form-group">
                    <label for="product__{{ $productStockPacketItem->product_id }}">{{ $productStockPacketItem->product->name }}</label>
                    <input type="number" class="form-control product" data-product-id="{{ $productStockPacketItem->product_id }}" id="product__{{ $productStockPacketItem->product_id }}" value="{{ $productStockPacketItem->quantity }}" name="packet_quantity_{{ $productStockPacketItem->product_id }}">
                </div>
            @endforeach
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
            <label for="packet_quantity">@lang('product_stocks.form.packet_product_quantity')</label>
            <input type="text" class="form-control" id="packet_product_quantity" name="packet_product_quantity">
            <input type="hidden" id="packet_id" value="{{ $productStockPacket->id }}">
        </div>
        <button id="product__assign" class="btn btn-success">@lang('product_stocks.form.buttons.add_product')</button>
        <button type="submit" id="update__packet" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@include('product_stocks.packets.modals.formModals')
@include('product_stocks.packets.includes.scripts')
