@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_payments.create')
        <a style="margin-left: 15px;" href="{{ action('OrdersController@edit', ["id" => $order->id]) }}"
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
    <form action="{{ action('OrdersPaymentsController@storeMaster') }}" method="POST">
        {{ csrf_field() }}
        <div class="firms-general" id="orderPayment">
            <div class="form-group">
                <label for="amount">@lang('order_payments.form.amount')</label>
                <input type="text" class="form-control" id="amount" name="amount"
                       value="{{ old('amount') }}">
            </div>
            <div class="form-group">
                <label for="notices">@lang('order_payments.form.notices')</label>
                <textarea class="form-control" id="notices" name="notices"
                          value="{{old('notices')}}" rows="5"></textarea>
            </div>
            <div class="form-group">
                <label for="promise">@lang('order_payments.form.promise')</label>
                @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 3)
                    <input type="checkbox" id="promise" name="promise">
                @else
                    <input type="checkbox" id="promise" name="promise" checked disabled="disabled">
                @endif
            </div>
            <div class="form-group">
                <label for="promise_date">Data wpłaty</label><br/>
                <input type="datetime" id="promise_date" name="created_at" value="{{ Carbon\Carbon::now() }}" class="form-control default-date-picker-now">
            </div>
            @if(!empty($order))
            <input type="hidden" value="{{ $order->id }}" name="order_id">
            <input type="hidden" value="{{ $order->customer_id }}" name="customer_id">
            @else
                <input type="hidden" value="{{ $customerId }}" name="customer_id">
            @endif
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@section('scripts')

@endsection
