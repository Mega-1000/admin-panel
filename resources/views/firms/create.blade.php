@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-company"></i> @lang('firms.create')
        <a style="margin-left: 15px;" href="{{ action('FirmsController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('firms.list')</span>
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
    <div class="tab" style="margin-bottom: 15px;">
        <button class="btn btn-primary active"
                name="change-button-form" id="button-general"
                value="general">@lang('firms.form.buttons.general')</button>
        <button class="btn btn-primary"
                name="change-button-form" id="button-address"
                value="address">@lang('firms.form.buttons.address')</button>
    </div>
    <form action="{{ action('FirmsController@store') }}" method="POST">
        {{ csrf_field() }}
        <div class="firms-general" id="general">
            <div class="form-group">
                <label for="name">@lang('firms.form.name')</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label for="short_name">@lang('firms.form.short_name')</label>
                <input class="form-control" id="short_name"
                          name="short_name"
                          value="{{ old('short_name') }}">
            </div>
            <div class="form-group">
                <label for="symbol">@lang('firms.form.symbol')</label>
                <input class="form-control" id="symbol"
                          name="symbol"
                          value="{{ old('symbol') }}"></textarea>
            </div>
            <div class="form-group">
                <label for="firm_type">@lang('firms.form.firm_type')</label>
                <select class="form-control text-uppercase" name="firm_type">
                    <option {{old('firm_type') === 'PRODUCTION' ? 'selected="selected"' : ''}} value="PRODUCTION">@lang('firms.form.production')</option>
                    <option {{old('firm_type') === 'DELIVERY' ? 'selected="selected"' : ''}} value="DELIVERY">@lang('firms.form.delivery')</option>
                    <option {{old('firm_type') === 'OTHER' ? 'selected="selected"' : ''}} value="OTHER">@lang('firms.form.other')</option>
                </select>
            </div>
            <div class="form-group">
                <label for="delivery_warehouse">@lang('firms.form.delivery_warehouse')</label>
                <input type="text" class="form-control" id="delivery_warehouse" name="delivery_warehouse"
                       value="{{ old('delivery_warehouse') }}">
            </div>
            <div class="form-group">
                <label for="email">@lang('firms.form.email')</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label for="secondary_email">@lang('firms.form.secondary_email')</label>
                <input type="email" class="form-control" id="secondary_email" name="secondary_email"
                       value="{{ old('secondary_email') }}">
            </div>
            <div class="form-group">
                <label for="nip">@lang('firms.form.nip')</label>
                <input type="text" class="form-control" id="nip" name="nip"
                       value="{{ old('nip') }}">
            </div>
            <div class="form-group">
                <label for="account_number">@lang('firms.form.account_number')</label>
                <input type="text" class="form-control" id="account_number" name="account_number"
                       value="{{ old('account_number') }}">
            </div>
            <div class="form-group">
                <label for="status">@lang('firms.form.status')</label>
                <select class="form-control text-uppercase" name="status">
                    <option value="ACTIVE">@lang('firms.form.active')</option>
                    <option value="PENDING">@lang('firms.form.pending')</option>
                </select>
            </div>
            <div class="form-group">
                <label for="phone">@lang('firms.form.phone')</label>
                <input type="text" class="form-control" id="phone" name="phone"
                       value="{{ old('phone') }}">
            </div>
            <div class="form-group">
                <label for="secondary_phone">@lang('firms.form.secondary_phone')</label>
                <input type="text" class="form-control" id="secondary_phone" name="secondary_phone"
                       value="{{ old('secondary_phone') }}">
            </div>
            <div class="form-group">
                <label for="notices">@lang('firms.form.notices')</label>
                <textarea cols="40" rows="5" type="text" class="form-control" id="notices" name="notices"
                       >{{ old('notices') }}</textarea>
            </div>
            <div class="form-group">
                <label for="secondary_notices">@lang('firms.form.secondary_notices')</label>
                <textarea cols="40" rows="5" type="text" class="form-control" id="secondary_notices" name="secondary_notices"
                       >{{ old('secondary_notices') }}</textarea>
            </div>
        </div>
        <div class="firms-address" id="address">
            <div class="form-group">
                <label for="postal_code">@lang('firms.form.address.postal_code')</label>
                <input type="text" class="form-control" id="postal_code" name="postal_code"
                       value="{{ old('postal_code') }}">
            </div>
            <div class="form-group">
                <label for="city">@lang('firms.form.address.city')</label>
                <input type="text" class="form-control" id="city" name="city"
                       value="{{ old('city') }}">
            </div>
            <div class="form-group">
                <label for="latitude">@lang('firms.form.address.latitude')</label>
                <input type="text" class="form-control" id="latitude" name="latitude"
                       value="{{ old('latitude') }}">
            </div>
            <div class="form-group">
                <label for="longitude">@lang('firms.form.address.longitude')</label>
                <input type="text" class="form-control" id="longitude" name="longitude"
                       value="{{ old('longitude') }}">
            </div>
            <div class="form-group">
                <label for="address">@lang('firms.form.address.address')</label>
                <input type="text" class="form-control" id="address" name="address"
                       value="{{ old('address') }}">
            </div>
            <div class="form-group">
                <label for="flat_number">@lang('firms.form.address.flat_number')</label>
                <input type="text" class="form-control" id="flat_number" name="flat_number"
                       value="{{ old('flat_number') }}">
            </div>
            <div class="form-group">
                <label for="address2">@lang('firms.form.address.address2')</label>
                <input type="text" class="form-control" id="address2" name="address2"
                       value="{{ old('address2') }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection

@section('datatable-scripts')
    <script>
        $(document).ready(function () {
            var general = $('#general').show();
            var address = $('#address').hide();
            var pageTitle = $('.page-title').children('i');
            var value;
            var breadcrumb = $('.breadcrumb:nth-child(2)');

            breadcrumb.children().remove();
            breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
            breadcrumb.append("<li class='active'><a href='/admin/firms/'>Firmy</a></li>");
            breadcrumb.append("<li class='disable'><a href='javascript:void()'>Dodaj</a></li>");
            $('[name="change-button-form"]').on('click', function () {
                value = this.value;
                $('#' + value).show();
                if (value === 'general') {
                    $('#button-general').addClass('active');
                    $('#button-address').removeClass('active');
                    address.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-company');
                } else if (value === 'address') {
                    $('#button-general').removeClass('active');
                    $('#button-address').addClass('active');
                    general.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-home');
                }
            });

        });
        $(function()
        {
            var available = [
                @php
                    foreach($warehouses as $item){
                         echo '"'.$item->symbol.'",';
                         }
                @endphp
                ];
            $( "#delivery_warehouse" ).autocomplete({
                source: available
            });
        });
    </script>
@endsection
