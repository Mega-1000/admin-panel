@extends('layouts.app')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-person"></i> @lang('customers.edit')
        <a style="margin-left:15px" href="{{ action('CustomersController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('customers.list')</span>
        </a>
    </h1>
@endsection

@section('app-content')
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
                                    value="general">@lang('customers.form.buttons.general')</button>
                            @if(App\Helpers\Helper::checkRole('customers', 'button-standard-address') === true)
                                <button class="btn btn-primary"
                                        name="change-button-form" id="button-standard-address"
                                        value="standard-address">@lang('customers.form.buttons.standard_address')</button>
                            @endif
                            @if(App\Helpers\Helper::checkRole('customers', 'button-invoice-address') === true)
                                <button class="btn btn-primary"
                                        name="change-button-form" id="button-invoice-address"
                                        value="invoice-address">@lang('customers.form.buttons.invoice_address')</button>
                            @endif
                            @if(App\Helpers\Helper::checkRole('customers', 'button-delivery-address') === true)
                                <button class="btn btn-primary"
                                        name="change-button-form" id="button-delivery-address"
                                        value="delivery-address">@lang('customers.form.buttons.delivery_address')</button>
                            @endif
                        </div>
                        <form action="{{ action('CustomersController@update', ['id' => $customer->id]) }}"
                              method="POST">
                            {{ csrf_field() }}
                            {{ method_field('put') }}
                            <div class="customer-general" id="general">
                                <div class="form-group">
                                    <label for="login">@lang('customers.form.login')</label>
                                    <input type="text" class="form-control" id="login" name="login" disabled="disabled"
                                           value="{{ $customer->login  }}">
                                </div>
                                <div class="form-group">
                                    <label for="password">@lang('customers.form.password')</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                           value="">
                                </div>
                                <div class="form-group">
                                    <label for="nick_allegro">@lang('customers.form.nick_allegro')</label>
                                    <input type="text" class="form-control" id="nick_allegro" name="nick_allegro"
                                           value="{{ $customer->nick_allegro ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label for="status">@lang('customers.form.status')</label>
                                    <select class="form-control text-uppercase" name="status">
                                        <option value="ACTIVE">@lang('customers.form.active')</option>
                                        <option value="PENDING">@lang('customers.form.pending')</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="is_staff">@lang('customers.form.is_staff')</label>
                                    <select class="form-control text-uppercase" name="is_staff">
                                        <option value="0" {{ $customer->is_staff == 0 ? 'selected' : '' }}>@lang('customers.form.no')</option>
                                        <option value="1" {{ $customer->is_staff == 1 ? 'selected' : '' }}>@lang('customers.form.yes')</option>
                                    </select>                                    
                                </div>
                            </div>
                            <div class="customer-address" id="standard-address">
                                @if(App\Helpers\Helper::checkRole('customers', 'standard_firstname') === true)
                                    <div class="form-group">
                                        <label for="standard_firstname">@lang('customers.form.standard_firstname')</label>
                                        <input type="text" class="form-control" id="standard_firstname"
                                               name="standard_firstname"
                                               value="{{ $customerAddressStandard->first->id->firstname ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'standard_lastname') === true)
                                    <div class="form-group">
                                        <label for="standard_lastname">@lang('customers.form.standard_lastname')</label>
                                        <input type="text" class="form-control" id="standard_lastname"
                                               name="standard_lastname"
                                               value="{{ $customerAddressStandard->first->id->lastname ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'standard_firmname') === true)
                                    <div class="form-group">
                                        <label for="standard_firmname">@lang('customers.form.standard_firmname')</label>
                                        <input type="text" class="form-control" id="standard_firmname"
                                               name="standard_firmname"
                                               value="{{ $customerAddressStandard->first->id->firmname ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'standard_nip') === true)
                                    <div class="form-group">
                                        <label for="standard_nip">@lang('customers.form.standard_nip')</label>
                                        <input type="text" class="form-control" id="standard_nip" name="standard_nip"
                                               value="{{ $customerAddressStandard->first->id->nip ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'standard_phone') === true)
                                    <div class="form-group">
                                        <label for="standard_phone">@lang('customers.form.standard_phone')</label>
                                        <input type="text" class="form-control" id="standard_phone"
                                               name="standard_phone"
                                               value="{{ $customerAddressStandard->first->id->phone ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'standard_address') === true)
                                    <div class="form-group">
                                        <label for="standard_address">@lang('customers.form.standard_address')</label>
                                        <input type="text" class="form-control" id="standard_address"
                                               name="standard_address"
                                               value="{{ $customerAddressStandard->first->id->address ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'standard_flat_number') === true)
                                    <div class="form-group">
                                        <label for="standard_flat_number">@lang('customers.form.standard_flat_number')</label>
                                        <input type="text" class="form-control" id="standard_flat_number"
                                               name="standard_flat_number"
                                               value="{{ $customerAddressStandard->first->id->flat_number ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'standard_city') === true)
                                    <div class="form-group">
                                        <label for="standard_city">@lang('customers.form.standard_city')</label>
                                        <input type="text" class="form-control" id="standard_city" name="standard_city"
                                               value="{{ $customerAddressStandard->first->id->city ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'standard_postal_code') === true)
                                    <div class="form-group">
                                        <label for="standard_postal_code">@lang('customers.form.standard_postal_code')</label>
                                        <input type="text" class="form-control" id="standard_postal_code"
                                               name="standard_postal_code"
                                               value="{{ $customerAddressStandard->first->id->postal_code ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'standard_email') === true)
                                    <div class="form-group">
                                        <label for="standard_email">@lang('customers.form.standard_email')</label>
                                        <input type="email" class="form-control" id="standard_email"
                                               name="standard_email"
                                               value="{{ $customerAddressStandard->first->id->email ?? '' }}">
                                    </div>
                                @endif
                            </div>
                            <div class="customer-address" id="invoice-address">
                                @if(App\Helpers\Helper::checkRole('customers', 'invoice_firstname') === true)
                                    <div class="form-group">
                                        <label for="invoice_firstname">@lang('customers.form.invoice_firstname')</label>
                                        <input type="text" class="form-control" id="invoice_firstname"
                                               name="invoice_firstname"
                                               value="{{ $customerAddressInvoice->first->id->firstname  ?? ''}}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'invoice_lastname') === true)
                                    <div class="form-group">
                                        <label for="invoice_lastname">@lang('customers.form.invoice_lastname')</label>
                                        <input type="text" class="form-control" id="invoice_lastname"
                                               name="invoice_lastname"
                                               value="{{ $customerAddressInvoice->first->id->lastname ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'invoice_firmname') === true)
                                    <div class="form-group">
                                        <label for="invoice_firmname">@lang('customers.form.invoice_firmname')</label>
                                        <input type="text" class="form-control" id="invoice_firmname"
                                               name="invoice_firmname"
                                               value="{{ $customerAddressInvoice->first->id->firmname ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'invoice_nip') === true)
                                    <div class="form-group">
                                        <label for="invoice_nip">@lang('customers.form.invoice_nip')</label>
                                        <input type="text" class="form-control" id="invoice_nip" name="invoice_nip"
                                               value="{{ $customerAddressInvoice->first->id->nip ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'invoice_phone') === true)
                                    <div class="form-group">
                                        <label for="invoice_phone">@lang('customers.form.invoice_phone')</label>
                                        <input type="text" class="form-control" id="invoice_phone" name="invoice_phone"
                                               value="{{ $customerAddressInvoice->first->id->phone ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'invoice_address') === true)
                                    <div class="form-group">
                                        <label for="invoice_address">@lang('customers.form.invoice_address')</label>
                                        <input type="text" class="form-control" id="invoice_address"
                                               name="invoice_address"
                                               value="{{ $customerAddressInvoice->first->id->address ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'invoice_flat_number') === true)
                                    <div class="form-group">
                                        <label for="invoice_flat_number">@lang('customers.form.invoice_flat_number')</label>
                                        <input type="text" class="form-control" id="invoice_flat_number"
                                               name="invoice_flat_number"
                                               value="{{ $customerAddressInvoice->first->id->flat_number ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'invoice_city') === true)
                                    <div class="form-group">
                                        <label for="invoice_city">@lang('customers.form.invoice_city')</label>
                                        <input type="text" class="form-control" id="invoice_city" name="invoice_city"
                                               value="{{ $customerAddressInvoice->first->id->city ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'invoice_postal_code') === true)
                                    <div class="form-group">
                                        <label for="invoice_postal_code">@lang('customers.form.invoice_postal_code')</label>
                                        <input type="text" class="form-control" id="invoice_postal_code"
                                               name="invoice_postal_code"
                                               value="{{ $customerAddressInvoice->first->id->postal_code ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'invoice_email') === true)
                                    <div class="form-group">
                                        <label for="invoice_email">@lang('customers.form.invoice_email')</label>
                                        <input type="email" class="form-control" id="invoice_email" name="invoice_email"
                                               value="{{ $customerAddressInvoice->first->id->email ?? '' }}">
                                    </div>
                                @endif
                            </div>
                            <div class="customer-address" id="delivery-address">
                                @if(App\Helpers\Helper::checkRole('customers', 'delivery_firstname') === true)
                                    <div class="form-group">
                                        <label for="delivery_firstname">@lang('customers.form.delivery_firstname')</label>
                                        <input type="text" class="form-control" id="delivery_firstname"
                                               name="delivery_firstname"
                                               value="{{ $customerAddressDelivery->first->id->firstname ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'delivery_lastname') === true)
                                    <div class="form-group">
                                        <label for="delivery_lastname">@lang('customers.form.delivery_lastname')</label>
                                        <input type="text" class="form-control" id="delivery_lastname"
                                               name="delivery_lastname"
                                               value="{{ $customerAddressDelivery->first->id->lastname ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'delivery_firmname') === true)
                                    <div class="form-group">
                                        <label for="delivery_firmname">@lang('customers.form.delivery_firmname')</label>
                                        <input type="text" class="form-control" id="delivery_firmname"
                                               name="delivery_firmname"
                                               value="{{ $customerAddressDelivery->first->id->firmname ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'delivery_nip') === true)
                                    <div class="form-group">
                                        <label for="delivery_nip">@lang('customers.form.delivery_nip')</label>
                                        <input type="text" class="form-control" id="delivery_nip" name="delivery_nip"
                                               value="{{ $customerAddressDelivery->first->id->nip ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'delivery_phone') === true)
                                    <div class="form-group">
                                        <label for="delivery_phone">@lang('customers.form.delivery_phone')</label>
                                        <input type="text" class="form-control" id="delivery_phone"
                                               name="delivery_phone"
                                               value="{{ $customerAddressDelivery->first->id->phone ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'delivery_address') === true)
                                    <div class="form-group">
                                        <label for="delivery_address">@lang('customers.form.delivery_address')</label>
                                        <input type="text" class="form-control" id="delivery_address"
                                               name="delivery_address"
                                               value="{{ $customerAddressDelivery->first->id->address ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'delivery_flat_number') === true)
                                    <div class="form-group">
                                        <label for="delivery_flat_number">@lang('customers.form.delivery_flat_number')</label>
                                        <input type="text" class="form-control" id="delivery_flat_number"
                                               name="delivery_flat_number"
                                               value="{{ $customerAddressDelivery->first->id->flat_number ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'delivery_city') === true)
                                    <div class="form-group">
                                        <label for="delivery_city">@lang('customers.form.delivery_city')</label>
                                        <input type="text" class="form-control" id="delivery_city" name="delivery_city"
                                               value="{{ $customerAddressDelivery->first->id->city ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'delivery_postal_code') === true)
                                    <div class="form-group">
                                        <label for="delivery_postal_code">@lang('customers.form.delivery_postal_code')</label>
                                        <input type="text" class="form-control" id="delivery_postal_code"
                                               name="delivery_postal_code"
                                               value="{{ $customerAddressDelivery->first->id->postal_code ?? '' }}">
                                    </div>
                                @endif
                                @if(App\Helpers\Helper::checkRole('customers', 'delivery_email') === true)
                                    <div class="form-group">
                                        <label for="delivery_email">@lang('customers.form.delivery_email')</label>
                                        <input type="email" class="form-control" id="delivery_email"
                                               name="delivery_email"
                                               value="{{ $customerAddressDelivery->first->id->email ?? '' }}">
                                    </div>
                                @endif
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
            var standardAddress = $('#standard-address').hide();
            var invoiceAddress = $('#invoice-address').hide();
            var deliveryAddress = $('#delivery-address').hide();
            var pageTitle = $('.page-title').children('i');
            var value;
            var breadcrumb = $('.breadcrumb:nth-child(2)');

            breadcrumb.children().remove();
            breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
            breadcrumb.append("<li class='active'><a href='/admin/customers/'>Klienci</a></li>");
            breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
            $('[name="change-button-form"]').on('click', function () {
                value = this.value;
                $('#' + value).show();
                if (value === 'general') {
                    $('#button-general').addClass('active');
                    $('#button-standard-address').removeClass('active');
                    $('#button-invoice-address').removeClass('active');
                    $('#button-delivery-address').removeClass('active');
                    standardAddress.hide();
                    invoiceAddress.hide();
                    deliveryAddress.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-person');
                } else if (value === 'standard-address') {
                    $('#button-general').removeClass('active');
                    $('#button-standard-address').addClass('active');
                    $('#button-invoice-address').removeClass('active');
                    $('#button-delivery-address').removeClass('active');
                    general.hide();
                    invoiceAddress.hide();
                    deliveryAddress.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-home');
                } else if (value === 'invoice-address') {
                    $('#button-general').removeClass('active');
                    $('#button-standard-address').removeClass('active');
                    $('#button-invoice-address').addClass('active');
                    $('#button-delivery-address').removeClass('active');
                    general.hide();
                    standardAddress.hide();
                    deliveryAddress.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-home');
                } else if (value === 'delivery-address') {
                    $('#button-general').removeClass('active');
                    $('#button-standard-address').removeClass('active');
                    $('#button-invoice-address').removeClass('active');
                    $('#button-delivery-address').addClass('active');
                    general.hide();
                    standardAddress.hide();
                    invoiceAddress.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-home');
                }
            });

        });
    </script>
@endsection
