@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('reports.edit')
        <a style="margin-left: 15px;" href="{{ route('planning.reports.index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('reports.list')</span>
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
    <form action="{{ action('ReportsController@update', ['id' => $report->id]) }}" method="post">
        {{ csrf_field() }}
        {{--{{ csrf_token() }}--}}
        <div class="statuses-general" id="general">
            <div class="form-group">
                <label for="name">@lang('reports.form.name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{$report->name}}">
            </div>
            <select name="user_id" class="form-control">
                @foreach($users as $user)
                    <option value="{{$user->id}}" @if($report->user_id == $user->id) selected @endif>{{$user->firstname}} {{$user->lastname}}</option>
                @endforeach
            </select>
            <div class="form-group">
                <label for="from">@lang('reports.form.from')</label>
                <input type="date" name="from" value="{{$report->from}}" class="form-control">
            </div>
            <div class="form-group">
                <label for="to">@lang('reports.form.to')</label>
                <input type="date" name="to" value="{{$report->to}}" class="form-control">
            </div>
            <div class="form-group">
                <label for="value">@lang('reports.form.value')</label>
                <input type="value" name="value" value="{{$report->value}}" class="form-control">
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
        breadcrumb.append("<li class='active'><a href='/admin/statuses/'>Raport</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
    </script>
@endsection
