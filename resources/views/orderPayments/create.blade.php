@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_payments.create')
        <a style="margin-left: 15px;" href="{{ action('OrdersController@edit', ["order_id" => $id]) }}"
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
    <form action="{{ action('OrdersPaymentsController@store') }}" method="POST">
        {{ csrf_field() }}
        <div class="firms-general" id="orderPayment">
            <div class="form-group">
                <label for="payment-type">Typ płatności</label>
                <select class="form-control" id="payment-type" name="payment-type">
                    <option value="CLIENT">@lang('order_payments.form.client')</option>
                    <option value="WAREHOUSE">@lang('order_payments.form.warehouse')</option>
                    <option value="SPEDITION">@lang('order_payments.form.spedition')</option>
                </select>
            </div>
            @if(Auth::user()->role_id == 3 || Auth::user()->role_id == 2 || Auth::user()->role_id == 1)
            <div class="hidden">
                <label for="tags">@lang('order_payments.form.promise')</label>
                <input type="checkbox" id="promise" name="promise" checked>
            </div>
            @else
                <div class="form-group">
                    <label for="tags">@lang('order_payments.form.promise')</label>
                    <input type="checkbox" id="promise" name="promise" checked readonly>
                </div>
            @endif
            <div class="form-group">
                <label for="promise_date">@lang('order_payments.form.promise_date')</label><br/>
                <input type="datetime" id="promise_date" name="promise_date" value="{{ Carbon\Carbon::now() }}" class="form-control default-date-picker-now">
            </div>
            <div class="form-group">
                Płatnik
                <select class="select2" data-live-search="true" name="payer">
                    <option value="{{ $order->customer()->first()->login }}">{{ $order->customer()->first()->login }}</option>

                    @foreach($firms as $firm)
                        <option value="{{ $firm->symbol }}">{{ $firm->symbol }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="declared_sum">@lang('order_payments.form.declared_sum')</label>
                <input type="text" class="form-control" id="declared_sum" name="declared_sum"
                       value="{{ old('declared_sum') }}">
            </div>
            <div class="form-group">
                <label for="comments">@lang('order_payments.form.comments')</label>
                <textarea class="form-control" id="comments" name="comments"
                          value="{{old('comments')}}" rows="5"></textarea>
            </div>
            <input type="hidden" value="{{ $id }}" name="order_id">
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@section('scripts')
    <script>
        var breadcrumb = $('.breadcrumb:nth-child(2)');

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/orders/{{$id}}/edit'>Płatności</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Dodaj</a></li>");
    </script>
@endsection
