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
    <form action="{{ action('ProductStockPacketsController@store', ['id' => $productStock->id]) }}" method="POST" onsubmit="return checkStockQuantity()">
        {{ csrf_field() }}
        <div class="product_stocks-general" id="general">
            <div class="form-group">
                <label for="name">@lang('product_stocks.form.name')</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="{{ $productStock->product->name }}" disabled>
            </div>
            <div class="form-group">
                <label for="symbol">@lang('product_stocks.form.symbol')</label>
                <input type="text" class="form-control" id="symbol" name="symbol"
                       value="{{ $productStock->product->symbol }}" disabled>
            </div>
            <div class="form-group">
                <label for="manufacturer">@lang('product_stocks.form.manufacturer')</label>
                <input type="text" class="form-control" id="manufacturer" name="manufacturer"
                       value="{{ $productStock->product->manufacturer }}" disabled>
            </div>
            <div class="form-group">
                <label for="packet_name">@lang('product_stocks.form.packet_name')</label>
                <input type="text" class="form-control" id="packet_name" name="packet_name">
            </div>
            <div class="form-group">
                <label for="packet_quantity">@lang('product_stocks.form.packet_quantity')</label>
                <input type="number" class="form-control" id="packet_quantity" name="packet_quantity">
            </div>
            <div class="form-group">
                <label for="packet_product_quantity">@lang('product_stocks.form.packet_product_quantity')</label>
                <input type="number" class="form-control" id="packet_product_quantity" name="packet_product_quantity">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@include('product_stocks.packets.modals.formModals')
@section('scripts')
    <script>
        function checkStockQuantity() {
            let productStockQuantity = {{ $productStock->quantity }};
            @if($productStock->position->first())
                let productStockFirstPositionQuantity = {{ $productStock->position->first()->position_quantity }};
            @else
                let productStockFirstPositionQuantity = null;
            @endif
            let packetQuantityResult = document.getElementById('packet_quantity').value * document.getElementById('packet_product_quantity').value;
            if(!productStockFirstPositionQuantity) {
                $('#stockPositionMissing').modal('show');
                return false;
            }
            if(productStockQuantity < packetQuantityResult) {
                $('#stockQuantityLow').modal('show');
                return false;
            }
            if(productStockFirstPositionQuantity < productStockQuantity) {
                $('#stockPositionQuantityLow').modal('show');
                return false;
            }
            return true;
        }
    </script>
@endsection
