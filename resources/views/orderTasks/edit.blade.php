@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_tasks.create')
        <a style="margin-left: 15px;" href="{{ action('OrdersController@edit', ["id" => $orderTask->order_id]) }}"
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
    <form action="{{ action('OrdersTasksController@update', $orderTask->id) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="firms-general" id="orderTask">
            <div class="form-group">
                <label for="title">@lang('order_tasks.form.title')</label>
                <input type="text" class="form-control" id="title" name="title"
                       value="{{ $orderTask->title }}">
            </div>
            <div class="form-group">
                <label for="description">@lang('order_tasks.form.description')</label>
                <input type="text" class="form-control" id="description" name="description"
                       value="{{ $orderTask->description }}">
            </div>
            <div class="form-group">
                <label for="status">@lang('order_tasks.form.employee')</label>
                <select name="employee_id" id="employee" class="form-control">
                    @foreach($employees as $employee)
                        @if($orderTask->employee_id == $employee->id)
                            <option value="{{ $employee->id }}" selected>{{ $employee->firstname }} {{ $employee->lastname }}</option>
                        @else
                            <option value="{{ $employee->id }}">{{ $employee->firstname }} {{ $employee->lastname }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="status">@lang('order_tasks.form.status')</label>
                <select name="status" id="status" class="form-control">
                    @if( $orderTask->status == 'OPEN' )
                        <option value="OPEN" selected>Otwarte</option>
                        <option value="CLOSED">Zamknięte</option>
                    @else
                        <option value="OPEN">Otwarte</option>
                        <option value="CLOSED" selected>Zamknięte</option>
                    @endif
                </select>
            </div>
            <div class="form-group">
                <label for="show_label_at">@lang('order_tasks.form.show_label_at')</label><br/>
                <input type="datetime" id="show_label_at" name="show_label_at" value="{{ $date }}" class="form-control">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@section('scripts')
    <script>
        var breadcrumb = $('.breadcrumb:nth-child(2)');

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/orders/{{$orderTask->order_id}}/edit'>Zadania</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
    </script>
@endsection