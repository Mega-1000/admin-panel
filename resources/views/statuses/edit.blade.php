@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('statuses.edit')
        <a style="margin-left: 15px;" href="{{ action('StatusesController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('statuses.list')</span>
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
    <form action="{{ action('StatusesController@update', ['id' => $status->id]) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="statuses-general" id="general">
            <div class="form-group">
                <label for="name">@lang('statuses.form.name')</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="{{ $status->name }}" track-write>
            </div>
            <div class="form-group">
                <label for="color">@lang('statuses.form.color')</label>
                <input type="text" class="form-control jscolor {onFineChange:'update(this)'}" id="color" name="color"
                       value="{{ $status->color }}" track-write>
            </div>
            <div class="form-group">
                <label for="tags">@lang('statuses.form.tags')</label>
                <ul class="tags">
                    @foreach($tags as $tag)
                        <li class="tag">
                            {{$tag->name}}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="form-group">
                <label for="message">@lang('statuses.form.message')</label>
                <textarea rows="10" cols="50" class="form-control" id="message"
                          name="message" track-write
                >{{ $status->message }}</textarea>
            </div>
            <div class="form-group">
                <label for="labels_to_add">@lang('statuses.form.labels_to_add')</label>
                <select multiple class="form-control text-uppercase" name="labels_to_add[]" size="8" track-click>
                    @foreach($labels as $label)
                        <option {{ in_array($label->id, $labelsToAddOnChange) ? 'selected="selected"' : '' }} value="{{ $label->id }}">{{ $label->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="status">@lang('statuses.form.status')</label>
                <select class="form-control text-uppercase" name="status" track-click>
                    <option value="ACTIVE">@lang('statuses.form.active')</option>
                    <option value="PENDING">@lang('statuses.form.pending')</option>
                </select>
            </div>
            <div class="form-group">
                <label for="labels_to_remove">Generuj ofertę</label>
                <!-- generate_order_offer -->
                <select class="form-control text-uppercase" name="generate_order_offer" track-click>
                    <option value="0" selected="{{ $status->generate_order_offer }}">Nie</option>
                    <option value="1" selected="{{ $status->generate_order_offer }}">Tak</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" track-click>@lang('voyager.generic.save')</button>
    </form>
    <div class="vue-components">
        <tracker :enabled="true" :user="{{ Auth::user()->id }}"/>
    </div>
@endsection
@section('scripts')
    <script src="{{URL::asset('js/jscolor.js')}}"></script>
    <script>
        var breadcrumb = $('.breadcrumb:nth-child(2)');

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/statuses/'>Statusy</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
    </script>
@endsection
