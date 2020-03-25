
@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-company"></i> @lang('firms.role_create')
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
    <form action="{{ action('EmployeeRoleController@store') }}" method="POST">
        {{ csrf_field() }}
        <div class="firms-general" id="general">
            <div class="form-group">
                <label for="name">@lang('firms.form.name')</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="{{ old('name') }}">
            </div>           
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection