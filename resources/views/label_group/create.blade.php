@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-character"></i> @lang('label_groups.create')
        <a style="margin-left: 15px;" href="{{ action('LabelGroupsController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('label_groups.list')</span>
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
    <form action="{{ action('LabelGroupsController@store') }}" method="POST">
        {{ csrf_field() }}
        <div class="label_groups-general" id="general">
            <div class="form-group">
                <label for="name">@lang('label_groups.form.name')</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="{{ old('name') }}">
                <label for="name">@lang('label_groups.form.order')</label>
                <input type="number" class="form-control" id="order" name="order"
                       value="{{ old('order') }}">
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
            breadcrumb.append("<li class='active'><a href='/admin/label-groups/'>Grupy etykiet</a></li>");
            breadcrumb.append("<li class='disable'><a href='javascript:void()'>Dodaj</a></li>");
    </script>
@endsection
