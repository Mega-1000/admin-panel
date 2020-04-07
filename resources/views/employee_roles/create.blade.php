@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-company"></i>@if (empty($role)) @lang('firms.role_create') @else @lang('firms.role_edit') @endif
        <a style="margin-left: 15px;" href="{{ action('EmployeeRoleController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('firms.list_role')</span>
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
    @if (empty($role))
        <form action="{{ action('EmployeeRoleController@store') }}" method="POST">
            @else
                <form action="{{ action('EmployeeRoleController@update', [$role->id]) }}" method="POST">
                    {{ method_field('PUT')}}
                    @endif
                    {{ csrf_field() }}
                    <div class="firms-general" id="general">
                        <div class="form-group">
                            <label for="name">@lang('firms.form.name')</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   @if (!empty($role))  value="{{ $role->name }}"@endif />
                        </div>
                        <div class="form-group">
                            <label for="name">@lang('firms.form.role_symbol')</label>
                            <input type="text" class="form-control" id="symbol" name="symbol"
                                   @if (!empty($role))  value="{{ $role->symbol }}"@endif />
                        </div>
                        <div class="form-group">
                            <label for="is_contact_displayed_in_fronted"> @lang('firms.form.role_symbol')</label>
                            <input type="checkbox" id="is_contact_displayed_in_fronted"
                                   name="is_contact_displayed_in_fronted"
                            @if (!empty($role))  {{ $role->is_contact_displayed_in_fronted ? "checked" : "" }}@endif />
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
                </form>
@endsection
