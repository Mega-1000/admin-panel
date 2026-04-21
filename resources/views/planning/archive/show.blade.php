@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-archive"></i> @lang('archive.show') {{$task->id}}
        <a style="margin-left: 15px;" href="{{ route('planning.archive.index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('archive.list')</span>
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
        <div class="statuses-general" id="general">
            <div class="form-group">
                <label for="name">@lang('reports.form.name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{$task->name}}" disabled>
            </div>
            <div class="form-group">
                <label for="warehouse_id">@lang('archive.form.warehouse_id')</label>
                <select class="form-control" name="warehouse_id" disabled>
                    <option value="">Wybierz</option>
                    @foreach($warehouses as $warehouse)
                        <option {{$task->warehouse_id === $warehouse->id ? 'selected="selected"' : ""}} value="{{$warehouse->id}}">{{$warehouse->symbol}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="user_id">@lang('archive.form.user_id')</label>
                <select class="form-control" name="user_id" disabled>
                    <option value="">Wybierz</option>
                    @foreach($users as $user)
                        <option {{$task->user_id === $user->id ? 'selected="selected"' : ""}} value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="user_id">@lang('archive.form.order_id')</label>
                <select class="form-control" name="order_id" disabled>
                    <option value="">Wybierz</option>
                    @foreach($orders as $order)
                        <option {{$task->order_id === $order->id ? 'selected="selected"' : ""}} value="{{$order->id}}">{{$order->id}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="date_start">@lang('archive.form.date_start')</label>
                <input class="form-control default-date-time-picker-now" name="date_start" type="text" value="{{$task->taskTime->date_start}}" disabled>
            </div>
            <div class="form-group">
                <label for="date_end">@lang('archive.form.date_end')</label>
                <input class="form-control default-date-time-picker-now" name="date_end" type="text" value="{{$task->taskTime->date_start}}" disabled>
            </div>
            <div class="form-group">
                <label for="color">@lang('archive.form.color')</label>
                <input type="text" class="form-control" id="color" name="color"
                       value="{{$task->color}}" disabled>
            </div>
        </div>
@endsection
@section('scripts')
    <script src="{{URL::asset('js/jscolor.js')}}"></script>
    <script>
        var breadcrumb = $('.breadcrumb:nth-child(2)');

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/planning/timetable'>Planowanie pracy</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/planning/archive'>Archiwum</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Zobacz</a></li>");
    </script>
@endsection
