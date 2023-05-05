@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_payments.create')
        <a style="margin-left: 15px;" href="{{ action('OrdersController@edit', ["order_id" => $orderPayment->order_id]) }}"
           class="btn btn-info install pull-right">
            <span>@lang('order_payments.list')</span>
        </a>
    </h1>
    <style>
        .tags {
            width: 100%;
        }
        .tag {
            width: 50%;
            float: right;
        }
    </style>
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css">

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
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
    <form action="{{ action('OrdersPaymentsController@update', ['id' => $orderPayment->id]) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="firms-general" id="orderPayment">
            <div class="form-group">
                <label for="amount">@lang('order_payments.form.amount')</label>
                <input type="text" class="form-control priceChange" id="amount" name="amount"
                       value="{{ $orderPayment->amount }}" >
            </div>
            <div class="form-group">
                <label for="notices">@lang('order_payments.form.notices')</label>
                <textarea class="form-control" id="notices" name="notices" rows="5">{{ $orderPayment->notices }}</textarea>
            </div>
            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 3)
            <div class="hidden">
                <label for="tags">@lang('order_payments.form.promise')</label>
                <input type="checkbox" id="promise" value="yes" name="promise" @if($orderPayment->promise == '1') checked @endif>
            </div>
            @endif
            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 3)
            <div class="form-group">
                <label for="promise_date">@lang('order_payments.form.promise_date')</label><br/>
                <input type="text" id="promise_date" name="promise_date" value="{{ $orderPayment->promise_date }}" class="form-control default-date-picker-now">
            </div>
            @endif
            <div class="form-group">
                Płatnik
                <select class="select2" data-live-search="true" name="payer">
                    <option value="{{ $orderPayment->order()->first()->customer()->first()->login }}">{{ $orderPayment->order()->first()->customer()->first()->login }}</option>

                    @foreach($firms as $firm)
                        <option value="{{ $firm->symbol }}">{{ $firm->symbol }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="tracking_number">@lang('order_payments.form.tracking_number')</label>
                <input type="text" class="form-control" id="tracking_number" name="tracking_number"
                       value="{{ $orderPayment->tracking_number }}">
            </div>
            <div class="form-group">
                <label for="operation_id">@lang('order_payments.form.operation_id')</label>
                <input type="text" class="form-control" id="operation_id" name="operation_id"
                       value="{{ $orderPayment->operation_id }}">
            </div>
            <div class="form-group">
                <label for="declared_sum">@lang('order_payments.form.declared_sum')</label>
                <input type="text" class="form-control" id="declared_sum" name="declared_sum"
                       value="{{ $orderPayment->declared_sum }}">
            </div>
            <div class="form-group">
                <label for="posting_date">@lang('order_payments.form.posting_date')</label><br/>
                <input type="datetime" id="posting_date" name="posting_date" value="{{ $orderPayment->posting_date }}" class="form-control default-date-picker-now">
            </div>
            <div class="form-group">
                <label for="operation_type">@lang('order_payments.form.operation_type')</label>
                <input type="text" class="form-control" id="operation_type" name="operation_type"
                       value="{{ $orderPayment->operation_type }}">
            </div>
            <div class="form-group">
                <label for="comments">@lang('order_payments.form.comments')</label>
                <textarea class="form-control" id="comments" name="comments" rows="5">
                    {{ $orderPayment->operation_type }}
                </textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@section('scripts')
    <script src="{{URL::asset('js/jscolor.js')}}"></script>
    <script>
        var breadcrumb = $('.breadcrumb:nth-child(2)');

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/orders/{{$id}}/edit'>Płatności</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
        function commaReplace(cssClass) {
            document.querySelectorAll(cssClass).forEach(function(input) {
                input.value = input.value.replace(/,/g, '.');
            });
        }

        $( document ).ready(function() {
            commaReplace('.priceChange');
        });
    </script>
@endsection
