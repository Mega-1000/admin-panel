@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('reports.create')
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
    <form action="{{ action('ReportsController@store') }}" method="post">
        {{ csrf_field() }}
        {{--{{ csrf_token() }}--}}
        <div class="statuses-general" id="general">
            <div class="form-group">
                <label for="name">@lang('reports.form.name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}">
            </div>
            @php
                $arraySelected = [];
            @endphp
            @if(old('users_id') != null)
            @foreach(old('users_id') as $userId)
                @php(array_push($arraySelected, $userId))
            @endforeach
            @endif
            <div class="form-group">
                <label for="users_id">Użytkownik</label>
                <select name="users_id[]" class="form-control" multiple>
                    @foreach($users as $user)
                        <option {{in_array($user->id, $arraySelected) == true ? 'selected="selected"' : ''}} value="{{$user->id}}">{{$user->firstname}} {{$user->lastname}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="from">@lang('reports.form.from')</label>
                <input type="text" value="{{old('from')}}" name="from" class="form-control default-date-picker-now">
            </div>
            <div class="form-group">
                <label for="to">@lang('reports.form.to')</label>
                <input type="text" name="to" value="{{old('to')}}" class="form-control default-date-picker-now">
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
        breadcrumb.append("<li class='active'><a href='/admin/statuses/'>Raporty</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Dodaj</a></li>");
    </script>
@endsection
