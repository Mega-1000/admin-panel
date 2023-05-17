@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_payments.create')
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
    <form action="{{ route('order_payments.create_return', ['order' => $id]) }}" method="POST">
        @csrf
        <div class="firms-general" id="orderPayment">
            <div class="form-group">
                <label for="payment-type">Typ płatności</label>
                <select class="form-control" id="payment-type" name="payment-type">
                    <option value="CLIENT">@lang('order_payments.form.client')</option>
                    <option value="WAREHOUSE">@lang('order_payments.form.warehouse')</option>
                    <option value="SPEDITION">@lang('order_payments.form.spedition')</option>
                </select>
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
                <label for="tags">@lang('order_payments.form.return_value')</label>
                <input type="text" class="form-control" id="return_value" name="return_value" checked>
            </div>
            <div class="form-group">
                <label for="notices">@lang('order_payments.form.notices')</label>
                <textarea class="form-control" id="notices" name="notices"
                          value="{{old('notices')}}" rows="5"></textarea>
            </div>
            <input type="hidden" value="{{ $id }}" name="order_id">
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
