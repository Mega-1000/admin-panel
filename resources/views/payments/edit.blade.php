@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_payments.create')
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
    <form action="{{ action('OrdersPaymentsController@paymentUpdate', ['id' => $payment->id]) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="firms-general" id="orderPayment">
            <div class="form-group">
                <label for="amount">@lang('order_payments.form.amount')</label>
                <input type="text" class="form-control priceChange" id="amount" name="amount"
                       value="{{ $payment->amount }}" >
            </div>
            <div class="form-group">
                <label for="created_at">Data wpłaty</label><br/>
                <input type="text" id="created_at" name="created_at" value="{{ $payment->created_at }}" class="form-control default-date-picker-now">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@section('scripts')
@endsection
