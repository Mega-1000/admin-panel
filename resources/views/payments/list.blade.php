@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-plus"></i> @lang('customers.payments.title')
        <a href="{!! route('customers.create') !!}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>@lang('customers.payments.create')</span>
        </a>
    </h1>
@endsection

@section('table')
    <div>
        <h3>@lang('customers.payments.history')</h3>
        <table style="width: 100%" id="dataTablePayments" class="table table-hover">
            <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>@lang('customer.payments.postedInSystemDate')</th>
                <th>@lang('customer.payments.postedInBankDate')</th>
                <th>@lang('customer.payments.paymentId')</th>
                <th>@lang('customer.payments.kindOfOperation')</th>
                <th>@lang('customer.payments.orderId')</th>
                <th>@lang('customer.payments.operator')</th>
                <th>@lang('customer.payments.value')</th>
                <th>@lang('customer.payments.balance')</th>
                <th>@lang('customer.payments.accountingNotes')</th>
                <th>@lang('customer.payments.transactionNotes')</th>
                <th>@lang('customer.payments.actions')</th>
            </tr>
            </thead>
        </table>
    </div>

@endsection

@section('datatable-scripts')

@endsection
