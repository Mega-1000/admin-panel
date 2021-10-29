@extends('layouts.app')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-plus"></i> @lang('transactions.title')
        <a href="{!! route('transactions.create') !!}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>@lang('transactions.create')</span>
        </a>
    </h1>
@endsection

@section('app-content')
    <div class="panel">
        <div class="vue-components panel-body">
            <transactions/>
        </div>
    </div>
@endsection
