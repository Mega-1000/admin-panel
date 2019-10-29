@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-pen"></i> @lang('tasks.create')
        <a style="margin-left: 15px;" href="{{ route('planning.tasks.index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('tasks.list')</span>
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
    <form action="{{ action('TasksController@store') }}" method="post">
        {{ csrf_field() }}
        <div class="statuses-general" id="general">
            <div class="form-group">
                <label for="name">@lang('reports.form.name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}">
            </div>
            <div class="form-group">
                <label for="warehouse_id">@lang('tasks.form.warehouse_id')</label>
                <select class="form-control" name="warehouse_id">
                    <option value="">Wybierz</option>
                    @foreach($warehouses as $warehouse)
                        <option {{old('warehouse_id') === $warehouse->id ? 'selected="selected"' : ""}} value="{{$warehouse->id}}">{{$warehouse->symbol}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="user_id">@lang('tasks.form.user_id')</label>
                <select class="form-control" name="user_id">
                    <option value="">Wybierz</option>
                    @foreach($users as $user)
                        <option {{old('user_id') === $user->id ? 'selected="selected"' : ""}} value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="user_id">@lang('tasks.form.order_id')</label>
                <select class="form-control" name="order_id">
                    <option value="">Wybierz</option>
                    @foreach($orders as $order)
                        <option {{old('order') === $order->id ? 'selected="selected"' : ""}} value="{{$order->id}}">{{$order->id}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="date_start">@lang('tasks.form.date_start')</label>
                <input class="form-control default-date-time-picker-now" name="date_start" type="text" value="{{old('date_start')}}">
            </div>
            <div class="form-group">
                <label for="date_end">@lang('tasks.form.date_end')</label>
                <input class="form-control default-date-time-picker-now" name="date_end" type="text" value="{{old('date_end')}}">
            </div>
            <div class="form-group">
                <label for="color">@lang('tasks.form.color')</label>
                <input type="text" class="form-control jscolor {onFineChange:'update(this)'}" id="color" name="color"
                       value="{{old('color')}}">
            </div>
            <div class="form-group">
                <label for="consultant_value">@lang('tasks.form.consultant_value')</label>
                <input type="text" name="consultant_value" id="consultant_value" class="form-control" value="{{old('consultant_value')}}">
            </div>
            <div class="form-group">
                <label for="consultant_notice">@lang('tasks.form.consultant_notice')</label>
                <textarea rows="5" cols="40" type="text" name="consultant_notice" id="consultant_notice"
                          class="form-control">{{old('consultant_notice')}}</textarea>
            </div>
            <div class="form-group">
                <label for="warehouse_value">@lang('tasks.form.warehouse_value')</label>
                <input type="text" name="warehouse_value" id="warehouse_value" class="form-control" value="{{old('warehouse_value')}}">
            </div>
            <div class="form-group">
                <label for="warehouse_notice">@lang('tasks.form.warehouse_notice')</label>
                <textarea rows="5" cols="40" type="text" name="warehouse_notice" id="warehouse_notice"
                          class="form-control">{{old('warehouse_notice')}}</textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@section('scripts')
    <script src="{{URL::asset('js/jscolor.js')}}"></script>
    <script>
        var breadcrumb = $('.breadcrumb:nth-child(2)');

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/planning/timetable'>Planowanie pracy</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/planning/tasks'>Wszystkie zadania</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Dodaj</a></li>");
    </script>
@endsection
