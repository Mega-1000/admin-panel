@extends('layouts.app')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-paint-bucket"></i> @lang('warehouses.edit')
        <a style="margin-left:15px" href="{{ action('FirmsController@edit', ['firm_id' => $warehouse->firm_id]) }}"
           class="btn btn-info install pull-right">
            <span>@lang('firms.back_to_edit')</span>
        </a>
    </h1>
@endsection

@section('app-content')
    <style>
        .open_days input {
            margin-left: 20px;
            width: 15%;
            display: inline;
        }

        .open_days label {
            width: 30%;
        }
    </style>
    <div class="browse container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    @if($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="panel-body">
                        <div style="margin-bottom: 15px;" class="tab">
                            <button class="btn btn-primary active"
                                    name="change-button-form" id="button-general"
                                    value="general">@lang('warehouses.form.buttons.general')</button>
                            <button class="btn btn-primary"
                                    name="change-button-form" id="button-address"
                                    value="address">@lang('warehouses.form.buttons.address')</button>
                            <button class="btn btn-primary"
                                    name="change-button-form" id="button-property"
                                    value="property">@lang('warehouses.form.buttons.property')</button>
                        </div>
                        <form action="{{ action('WarehousesController@update', ['id' => $warehouse->id]) }}"
                              method="POST">
                            {{ csrf_field() }}
                            {{ method_field('put') }}
                            <div class="warehouses-general" id="general">
                                <div class="form-group">
                                    <label for="symbol">@lang('warehouses.form.symbol')</label>
                                    <input type="text" class="form-control" id="symbol" name="symbol"
                                           value="{{ $warehouse->symbol }}">
                                </div>
                                <div class="form-group">
                                    <label for="status">@lang('warehouses.form.status')</label>
                                    <select class="form-control text-uppercase" name="status">
                                        <option value="ACTIVE">@lang('warehouses.form.active')</option>
                                        <option value="PENDING">@lang('warehouses.form.pending')</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="radius">Zasięg działania</label>
                                    <input type="number" class="form-control" id="radius" name="radius"
                                           value="{{ $warehouse->radius }}">
                                </div>
                            </div>
                            <div class="warehouses-address" id="address">
                                <div class="form-group">
                                    <label for="address">@lang('warehouses.form.address')</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                           value="{{ $warehouseAddress->address }}">
                                </div>
                                <div class="form-group">
                                    <label for="warehouse_number">@lang('warehouses.form.warehouse_number')</label>
                                    <input type="text" class="form-control" id="warehouse_number"
                                           name="warehouse_number"
                                           value="{{$warehouseAddress->warehouse_number }}">
                                </div>
                                <div class="form-group">
                                    <label for="postal_code">@lang('warehouses.form.postal_code')</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code"
                                           value="{{ $warehouseAddress->postal_code }}">
                                </div>
                                <div class="form-group">
                                    <label for="city">@lang('warehouses.form.city')</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                           value="{{ $warehouseAddress->city }}">
                                </div>
                            </div>
                            <div class="warehouses-property" id="property">
                                <div class="form-group">
                                    <label for="firstname">@lang('warehouses.form.firstname')</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname"
                                           value="{{ $warehouseProperty->firstname }}">
                                </div>
                                <div class="form-group">
                                    <label for="lastname">@lang('warehouses.form.lastname')</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname"
                                           value="{{ $warehouseProperty->lastname }}">
                                </div>
                                <div class="form-group">
                                    <label for="phone">@lang('warehouses.form.phone')</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                           value="{{ $warehouseProperty->phone }}">
                                </div>
                                <div class="form-group">
                                    <label for="comments">@lang('warehouses.form.comments')</label>
                                    <textarea rows="4" cols="50" class="form-control" id="comments"
                                              name="comments">{{ $warehouseProperty->comments }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="additional_comments">@lang('warehouses.form.additional_comments')</label>
                                    <textarea rows="4" cols="50" class="form-control" id="additional_comments"
                                              name="additional_comments">{{ $warehouseProperty->additional_comments }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="open_days">@lang('warehouses.form.open_days')</label>
                                    <div class="open_days">
                                        <label for="open_days_monday_from">@lang('warehouses.form.monday')</label>
                                        <span><strong>@lang('warehouses.form.from')</strong></span> <input type="text"
                                                                                                           class="form-control"
                                                                                                           id="open_days_monday_from"
                                                                                                           name="open_days_monday_from"
                                                                                                           value="{{ $openDays['monday']['from'] }}">
                                        <span><strong>@lang('warehouses.form.to')</strong></span> <input type="text"
                                                                                                         class="form-control"
                                                                                                         id="open_days_monday_to"
                                                                                                         name="open_days_monday_to"
                                                                                                         value="{{ $openDays['monday']['to'] }}">
                                    </div>
                                    <div class="open_days">
                                        <label for="open_days">@lang('warehouses.form.tuesday')</label>
                                        <span><strong>@lang('warehouses.form.from')</strong></span> <input type="text"
                                                                                                           class="form-control"
                                                                                                           id="open_days_tuesday_from"
                                                                                                           name="open_days_tuesday_from"
                                                                                                           value="{{ $openDays['tuesday']['from'] }}">
                                        <span><strong>@lang('warehouses.form.to')</strong></span> <input type="text"
                                                                                                         class="form-control"
                                                                                                         id="open_days_tuesday_to"
                                                                                                         name="open_days_tuesday_to"
                                                                                                         value="{{ $openDays['tuesday']['to'] }}">
                                    </div>
                                    <div class="open_days">
                                        <label for="open_days">@lang('warehouses.form.wednesday')</label>
                                        <span><strong>@lang('warehouses.form.from')</strong></span> <input type="text"
                                                                                                           class="form-control"
                                                                                                           id="open_days_wednesday_from"
                                                                                                           name="open_days_wednesday_from"
                                                                                                           value="{{ $openDays['wednesday']['from'] }}">
                                        <span><strong>@lang('warehouses.form.to')</strong></span> <input type="text"
                                                                                                         class="form-control"
                                                                                                         id="open_days_wednesday_to"
                                                                                                         name="open_days_wednesday_to"
                                                                                                         value="{{ $openDays['wednesday']['to'] }}">
                                    </div>
                                    <div class="open_days">
                                        <label for="open_days">@lang('warehouses.form.thursday')</label>
                                        <span><strong>@lang('warehouses.form.from')</strong></span> <input type="text"
                                                                                                           class="form-control"
                                                                                                           id="open_days_thursday_from"
                                                                                                           name="open_days_thursday_from"
                                                                                                           value="{{ $openDays['thursday']['from'] }}">
                                        <span><strong>@lang('warehouses.form.to')</strong></span> <input type="text"
                                                                                                         class="form-control"
                                                                                                         id="open_days_thursday_to"
                                                                                                         name="open_days_thursday_to"
                                                                                                         value="{{ $openDays['thursday']['to'] }}">
                                    </div>
                                    <div class="open_days">
                                        <label for="open_days">@lang('warehouses.form.friday')</label>
                                        <span><strong>@lang('warehouses.form.from')</strong></span> <input type="text"
                                                                                                           class="form-control"
                                                                                                           id="open_days_friday_from"
                                                                                                           name="open_days_friday_from"
                                                                                                           value="{{ $openDays['friday']['from'] }}">
                                        <span><strong>@lang('warehouses.form.to')</strong></span> <input type="text"
                                                                                                         class="form-control"
                                                                                                         id="open_days_friday_to"
                                                                                                         name="open_days_friday_to"
                                                                                                         value="{{ $openDays['friday']['to'] }}">
                                    </div>
                                    <div class="open_days">
                                        <label for="open_days">@lang('warehouses.form.saturday')</label>
                                        <span><strong>@lang('warehouses.form.from')</strong></span> <input type="text"
                                                                                                           class="form-control"
                                                                                                           id="open_days_saturday_from"
                                                                                                           name="open_days_saturday_from"
                                                                                                           value="{{ $openDays['saturday']['from'] }}">
                                        <span><strong>@lang('warehouses.form.to')</strong></span> <input type="text"
                                                                                                         class="form-control"
                                                                                                         id="open_days_saturday_to"
                                                                                                         name="open_days_saturday_to"
                                                                                                         value="{{ $openDays['saturday']['to'] }}">
                                    </div>
                                    <div class="open_days">
                                        <label for="open_days">@lang('warehouses.form.sunday')</label>
                                        <span><strong>@lang('warehouses.form.from')</strong></span> <input type="text"
                                                                                                           class="form-control"
                                                                                                           id="open_days_sunday_from"
                                                                                                           name="open_days_sunday_from"
                                                                                                           value="{{ $openDays['sunday']['from'] }}">
                                        <span><strong>@lang('warehouses.form.to')</strong></span> <input type="text"
                                                                                                         class="form-control"
                                                                                                         id="open_days_sunday_to"
                                                                                                         name="open_days_sunday_to"
                                                                                                         value="{{ $openDays['sunday']['to'] }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email">@lang('warehouses.form.email')</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="{{ $warehouseProperty->email }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            var general = $('#general').show();
            var address = $('#address').hide();
            var property = $('#property').hide();
            var pageTitle = $('.page-title').children('i');
            var value;
            var breadcrumb = $('.breadcrumb:nth-child(2)');

            breadcrumb.children().remove();
            breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
            breadcrumb.append("<li class='active'><a href='/admin/firms/{{$warehouse->firm_id}}/edit'>Firmy</a></li>");
            breadcrumb.append("<li class='active'><a href='/admin/firms/{{$warehouse->firm_id}}/edit#warehouses'>Magazyny</a></li>");
            breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");

            $('[name="change-button-form"]').on('click', function () {
                value = this.value;
                $('#' + value).show();
                if (value === 'general') {
                    $('#button-general').addClass('active');
                    $('#button-address').removeClass('active');
                    $('#button-property').removeClass('active');
                    address.hide();
                    property.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-paint-bucket');
                } else if (value === 'address') {
                    $('#button-general').removeClass('active');
                    $('#button-address').addClass('active');
                    $('#button-property').removeClass('active');
                    general.hide();
                    property.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-home');
                } else if (value === 'property') {
                    $('#button-general').removeClass('active');
                    $('#button-address').removeClass('active');
                    $('#button-property').addClass('active');
                    general.hide();
                    address.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-documentation');
                }
            });

        });

    </script>
@endsection