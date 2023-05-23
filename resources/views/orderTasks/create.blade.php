@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_tasks.create')
        <a style="margin-left: 15px;" href="{{ action('OrdersController@edit', ["order_id" => $id]) }}"
           class="btn btn-info install pull-right">
            <span>@lang('order_tasks.list')</span>
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
    <form action="{{ action('OrdersTasksController@store') }}" method="POST">
        {{ csrf_field() }}
        <div class="firms-general" id="orderTask">
            <div class="form-group">
                <label for="title">@lang('order_tasks.form.title')</label>
                <input type="text" class="form-control" id="title" name="title"
                       value="{{ old('title') }}">
            </div>
            <div class="form-group">
                <label for="description">@lang('order_tasks.form.description')</label>
                <input type="text" class="form-control" id="description" name="description"
                       value="{{old('description')}}">
            </div>
            <div class="form-group">
                <label for="status">@lang('order_tasks.form.employee')</label>
                <select name="employee_id" id="employee" class="form-control">
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->firstname }} {{ $employee->lastname }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="status">@lang('order_tasks.form.status')</label>
                <select name="status" id="status" class="form-control">
                    <option value="OPEN">Otwarte</option>
                    <option value="CLOSED">Zamknięte</option>
                </select>
            </div>
            <div class="form-group">
                <label for="show_label_at">@lang('order_tasks.form.show_label_at')</label><br/>
                <input type="date" id="show_label_at" name="show_label_at" class="form-control">
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
        breadcrumb.append("<li class='active'><a href='/admin/orders/{{$id}}/edit'>Zadania</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Dodaj</a></li>");
    </script>
@endsection