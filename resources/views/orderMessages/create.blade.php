@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_messages.create')
        <a style="margin-left: 15px;" href="{{ action('OrdersController@edit', ["id" => $id]) }}"
           class="btn btn-info install pull-right">
            <span>@lang('order_messages.list')</span>
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
    <form action="{{ action('OrdersMessagesController@store') }}" method="POST">
        {{ csrf_field() }}
        <div class="firms-general" id="orderTask">
            <div class="form-group">
                <label for="title">@lang('order_messages.form.title')</label>
                <input type="text" class="form-control" id="title" name="title"
                       value="{{ old('title') }}">
            </div>
            <div class="form-group">
                <label for="message">@lang('order_messages.form.description')</label>
                <input type="text" class="form-control" id="message" name="message"
                       value="{{old('message')}}">
            </div>
            <div class="form-group">
                <label for="status">@lang('order_messages.form.employee')</label>
                <select name="employee_id" id="employee" class="form-control">
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->firstname }} {{ $employee->lastname }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="status">@lang('order_message.form.status')</label>
                <select name="status" id="status" class="form-control">
                    <option value="OPEN">Otwarte</option>
                    <option value="CLOSED">Zamknięte</option>
                </select>
            </div>
            <div class="form-group">
                <label for="type">@lang('order_message.form.type')</label>
                <select name="type" id="type" class="form-control">
                    <option value="GENERAL">Ogólne</option>
                    <option value="SHIPPING">Wysyłka</option>
                    <option value="WAREHOUSE">Magazyn</option>
                    <option value="COMPLAINT">Skarga</option>
                </select>
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
        breadcrumb.append("<li class='active'><a href='/admin/orders/{{$id}}/edit'>Wiadomości</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Dodaj</a></li>");
    </script>
@endsection