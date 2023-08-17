@extends('layouts.app')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-eye"></i> @lang('product_stock_logs.show_log') {{$productStockLog->created_at}}
        <a style="margin-left: 15px;" href="{{ route('product_stocks.edit', ['id' => $id]) }}"
           class="btn btn-info install pull-right">
            <span>@lang('product_stock_positions.list')</span>
        </a>
    </h1>
@endsection

@section('app-content')
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="panel-body">
        <div class="product_stock_logs-general" id="general">
            <div class="form-group">
                <label for="product_stock_id">@lang('product_stock_logs.form.product_stock_id')</label>
                <input type="text" class="form-control" id="product_stock_id" name="product_stock_id"
                       value="{{ $productStockLog->product_stock_id }}" disabled>
            </div>
            <div class="form-group">
                <label for="product_stock_position_id">@lang('product_stock_logs.form.product_stock_position_id')</label>
                <input type="text" class="form-control" id="product_stock_position_id" name="product_stock_position_id"
                       value="{{ $productStockLog->product_stock_position_id }}" disabled>
            </div>
            <div class="form-group">
                <label for="action">@lang('product_stock_logs.form.action')</label>
                <input @if($productStockLog->action === 'ADD')
                       style="background-color: green; color: #fff"
                       @else
                       style="background-color: red; color: #fff"
                       @endif
                       type="text" class="form-control" id="action" name="action"
                       value="{{ $productStockLog->action === 'ADD' ? __('product_stock_logs.form.add') : __('product_stock_logs.form.delete')}}"
                       disabled>
            </div>
            <div class="form-group">
                <label for="quantity">@lang('product_stock_logs.form.quantity')</label>
                <input type="text" class="form-control" id="quantity" name="quantity"
                       value="{{ $productStockLog->quantity }}" disabled>
            </div>
            <div class="form-group">
                <label for="name">@lang('product_stock_logs.form.username')</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="{{ $productStockLog->user->name }}" disabled>
            </div>
            <div class="form-group">
                <label for="firstname">@lang('product_stock_logs.form.firstname')</label>
                <input type="text" class="form-control" id="firstname" name="firstname"
                       value="{{ $productStockLog->user->firstname }}" disabled>
            </div>
            <div class="form-group">
                <label for="lastname">@lang('product_stock_logs.form.lastname')</label>
                <input type="text" class="form-control" id="lastname" name="lastname"
                       value="{{ $productStockLog->user->lastname }}" disabled>
            </div>
            <form action="{{ route('product-stock-logs.update', $productStockLog->id) }}">
                <div class="form-group">
                    <label for="comments">@lang('product_stock_logs.form.comments')</label>
                    <input type="text" class="form-control" id="comments" name="comments"
                           value="{{ $productStockLog->comments }}">
                </div>

                <button class="btn btn-primary">
                    Zapisz
                </button>
            </form>

            @endsection

            @section('javascript')
                <script>
                    $(document).ready(function () {
                        const breadcrumb = $('.breadcrumb:nth-child(2)');

                        breadcrumb.children().remove();
                        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
                        breadcrumb.append("<li class='active'><a href='/admin/product/stocks'>Stany magazynowe</a></li>");
                        breadcrumb.append("<li class='disable'><a href='/admin/product/stocks/{{$id}}/edit'>Edytuj</a></li>");
                        breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$id}}/edit#logs'>Historia zmian</a></li>");
                        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Zobacz</a></li>");
                </script>
@endsection
