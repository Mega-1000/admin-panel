@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-character"></i> @lang('labels.create')
        <a style="margin-left: 15px;" href="{{ action('LabelsController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('labels.list')</span>
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
    <form action="{{ action('LabelsController@store') }}" method="POST">
        {{ csrf_field() }}
        <div class="labels-general" id="general">
            <div class="form-group">
                <label for="name">@lang('labels.form.name')</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label for="name">@lang('labels.table.order')</label>
                <input type="text" class="form-control" id="order" name="order"
                       value="{{ old('order') }}">
            </div>
            <div class="form-group">
                <label for="label-group">@lang('labels.form.label_group')</label>
                <select class="form-control text-uppercase" name="label_group_id">
                    <option value="" selected="selected">--BRAK GRUPY--</option>
                    @foreach($labelGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="color">@lang('labels.form.color')</label>
                <input type="text" class="form-control jscolor {onFineChange:'update(this)'}" id="color" name="color"
                       value="{{old('color')}}">
            </div>
            <div class="form-group">
                <label for="font_color">@lang('labels.form.font_color')</label>
                <input type="text" class="form-control jscolor {onFineChange:'update(this)'}" id="font_color" name="font_color"
                       value="{{old('font_color')}}">
            </div>
            <div class="form-group">
                <label for="name">@lang('labels.form.icon_name') - <a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank">(@lang('labels.form.icon_list'))</a></label>
                <input type="text" class="form-control" id="icon_name" name="icon_name"
                       value="{{ old('icon_name') }}">
            </div>
            <div class="form-group">
                <label for="status">@lang('labels.form.status')</label>
                <select class="form-control text-uppercase" name="status">
                    <option value="ACTIVE">@lang('labels.form.active')</option>
                    <option value="PENDING">@lang('labels.form.pending')</option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">@lang('labels.form.message')</label>
                <textarea type="text" class="form-control" id="message" name="message"
                        rows="20">{{ old('message') }}</textarea>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="manual_label_selection_to_add_after_removal" name="manual_label_selection_to_add_after_removal"
                       value="1">
                <label for="manual_label_selection_to_add_after_removal" class="form-check-label">@lang('labels.form.manual_label_selection_to_add_after_removal')</label>
            </div>
            <div class="form-group">
                <label for="labels_to_add_after_addition">@lang('labels.form.labels_to_add_after_addition')</label>
                <select multiple class="form-control text-uppercase" name="labels_to_add_after_addition[]">
                    @foreach($labels as $label)
                        <option value="{{ $label->id }}">{{ $label->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="labels_to_add_after_removal">@lang('labels.form.labels_to_add_after_removal')</label>
                <select multiple class="form-control text-uppercase" name="labels_to_add_after_removal[]">
                    @foreach($labels as $label)
                        <option value="{{ $label->id }}">{{ $label->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="labels_to_remove_after_addition">@lang('labels.form.labels_to_remove_after_addition')</label>
                <select multiple class="form-control text-uppercase" name="labels_to_remove_after_addition[]">
                    @foreach($labels as $label)
                        <option value="{{ $label->id }}">{{ $label->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="labels_to_remove_after_removal">@lang('labels.form.labels_to_remove_after_removal')</label>
                <select multiple class="form-control text-uppercase" name="labels_to_remove_after_removal[]">
                    @foreach($labels as $label)
                        <option value="{{ $label->id }}">{{ $label->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="isTimed" name="isTimed">
                <label for="isTimed" class="form-check-label">@lang('labels.form.timed')</label>
            </div>
            <timed-labels-config :name="'timed_labels'" :labels="{{ json_encode($labels) }}"/>
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
            breadcrumb.append("<li class='active'><a href='/admin/labels/'>Etykiety</a></li>");
            breadcrumb.append("<li class='disable'><a href='javascript:void()'>Dodaj</a></li>");
    </script>
@endsection
