
@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-company"></i>@if (empty($contentType)) @lang('order_packages.content_create') @else @lang('order_packages.content_edit') @endif
        <a style="margin-left: 15px;" href="{{ action('ContentTypesController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('order_packages.content_list')</span>
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
    @if (empty($contentType))
    <form action="{{ action('ContentTypesController@store') }}" method="POST">
    @else 
    <form action="{{ action('ContentTypesController@update', [$contentType->id]) }}" method="POST">
    {{ method_field('PUT')}}
    @endif
        {{ csrf_field() }}
        <div class="firms-general" id="general">
            <div class="form-group">
                <label for="name">@lang('order_packages.form.content_type_name')</label>
                <input type="text" class="form-control" id="name" name="name"
                     @if (!empty($contentType))  value="{{ $contentType->name }}"@endif>
            </div> 
            <div class="form-group">
            <label for="name">@lang('firms.form.role_symbol')</label>
            <input type="text" class="form-control" id="symbol" name="symbol"
                  @if (!empty($contentType))  value="{{ $contentType->symbol }}"@endif>
            </div>
        </div>      
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection