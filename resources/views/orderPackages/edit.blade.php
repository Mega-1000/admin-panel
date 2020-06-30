@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_packages.edit')
        <a style="margin-left: 15px;" href="{{ action('OrdersController@edit', ["id" => $orderPackage->order_id]) }}"
           class="btn btn-info install pull-right">
            <span>@lang('order_packages.list')</span>
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
    <form action="{{ action('OrdersPackagesController@update', $id) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="firms-general" id="orderPackage">
            <div class="form-group">
                <label for="size_b">@lang('order_packages.form.chosen_data_template_for_edit')</label>
                <input type="text" class="form-control" disabled="disabled"
                       value="{{ $orderPackage->chosen_data_template }}">
            </div>
            <div class="form-group">
                <label for="symbol">@lang('order_packages.form.symbol')</label>
                <input type="text" class="form-control" disabled="disabled"
                       value="{{ $orderPackage->symbol }}">
            </div>
            <div class="form-group">
                <label for="size_a">@lang('order_packages.form.size_a')</label>
                <input type="text" class="form-control" id="size_a" name="size_a"
                       value="{{ $orderPackage->size_a }}">
            </div>
            <div class="form-group">
                <label for="size_b">@lang('order_packages.form.size_a')</label>
                <input type="text" class="form-control" id="size_b" name="size_b"
                       value="{{ $orderPackage->size_b }}">
            </div>
            <div class="form-group">
                <label for="size_c">@lang('order_packages.form.size_c')</label>
                <input type="text" class="form-control" id="size_c" name="size_c"
                       value="{{ $orderPackage->size_c }}">
            </div>
            <div class="form-group">
                <label for="shipment_date">@lang('order_packages.form.shipment_date')</label><br/>
                <input type="text" id="shipment_date" name="shipment_date" class="form-control default-date-picker-now"
                       value="{{ $orderPackage->shipment_date }}">
            </div>
            <div class="form-group">
                <label for="delivery_date">@lang('order_packages.form.delivery_date')</label><br/>
                <input type="text" id="delivery_date" name="delivery_date" class="form-control default-date-picker-now"
                       value="{{ $orderPackage->delivery_date }}">
            </div>
            <div class="form-group">
                <label for="service_courier_name">@lang('order_packages.form.service_courier_name')</label>
                <select class="form-control" id="service_courier_name" name="service_courier_name">
                    <option {{ $orderPackage->service_courier_name === 'INPOST' ? 'selected="selected"' : '' }} value="INPOST">
                        INPOST
                    </option>
                    <option {{ $orderPackage->service_courier_name === 'POCZTEX' ? 'selected="selected"' : '' }} value="POCZTEX">
                        POCZTEX
                    </option>
                    <option {{ $orderPackage->service_courier_name === 'DPD' ? 'selected="selected"' : '' }} value="DPD">
                        DPD
                    </option>
                    <option {{ $orderPackage->service_courier_name === 'APACZKA' ? 'selected="selected"' : '' }} value="APACZKA">
                        APACZKA
                    </option>
                    <option {{ $orderPackage->service_courier_name === 'JAS' ? 'selected="selected"' : '' }} value="JAS">
                        JAS
                    </option>
                    <option {{ $orderPackage->service_courier_name === 'GIELDA' ? 'selected="selected"' : '' }} value="GIELDA">
                        GIELDA
                    </option>
                    <option {{ $orderPackage->service_courier_name === 'ODBIOR_OSOBISTY' ? 'selected="selected"' : '' }} value="ODBIOR_OSOBISTY">
                        ODBIÓR OSOBISTY
                    </option>
                    <option {{ $orderPackage->service_courier_name == 'GLS' ? 'selected="selected"' : '' }} value="GLS">GLS</option>
                    <option {{ $orderPackage->service_courier_name == 'UPS' ? 'selected="selected"' : '' }} value="UPS">UPS</option>
                    <option {{ old('delivery_courier_name') == 'ODBIOR_OSOBISTY' ? 'selected="selected"' : '' }} value="ODBIOR_OSOBISTY">
                        PACZKOMAT
                    </option>
                    @if ($isAllegro)
                     <option {{ $orderPackage->service_courier_name === 'ALLEGRO-INPOST' ? 'selected="selected"' : '' }} value="ALLEGRO-INPOST">
                        ALLEGRO-INPOST
                    </option>
                    @endif
                </select>
            </div>
            <div class="form-group">
                <label for="delivery_courier_name">@lang('order_packages.form.delivery_courier_name')</label>
                <select class="form-control" id="delivery_courier_name" name="delivery_courier_name">
                    <option {{ $orderPackage->delivery_courier_name === 'INPOST' ? 'selected="selected"' : '' }} value="INPOST">
                        INPOST
                    </option>
                    <option {{ $orderPackage->delivery_courier_name === 'POCZTEX' ? 'selected="selected"' : '' }} value="POCZTEX">
                        POCZTEX
                    </option>
                    <option {{ $orderPackage->delivery_courier_name === 'DPD' ? 'selected="selected"' : '' }} value="DPD">
                        DPD
                    </option>
                    <option {{ $orderPackage->delivery_courier_name === 'APACZKA' ? 'selected="selected"' : '' }} value="APACZKA">
                        APACZKA
                    </option>
                    <option {{ $orderPackage->delivery_courier_name === 'JAS' ? 'selected="selected"' : '' }} value="JAS">
                        JAS
                    </option>
                    <option {{ $orderPackage->delivery_courier_name === 'GIELDA' ? 'selected="selected"' : '' }} value="GIELDA">
                        GIELDA
                    </option>
                    <option {{ $orderPackage->service_courier_name == 'GLS' ? 'selected="selected"' : '' }} value="GLS">GLS</option>
                    <option {{ $orderPackage->service_courier_name == 'UPS' ? 'selected="selected"' : '' }} value="UPS">UPS</option>
                    <option {{ $orderPackage->delivery_courier_name === 'ODBIOR_OSOBISTY' ? 'selected="selected"' : '' }} value="ODBIOR_OSOBISTY">
                        ODBIÓR OSOBISTY
                    </option>
                    <option {{ $orderPackage->delivery_courier_name === 'PACZKOMAT' ? 'selected="selected"' : '' }} value="PACZKOMAT">
                        PACZKOMAT
                    </option>
                    @if ($isAllegro))
                     <option {{ $orderPackage->delivery_courier_name === 'ALLEGRO-INPOST' ? 'selected="selected"' : '' }} value="ALLEGRO-INPOST">
                        ALLEGRO-INPOST
                    </option>
                    @endif
                </select>
            </div>
            <div class="form-group">
                <label for="weight">@lang('order_packages.form.weight')</label>
                <input type="text" class="form-control" id="weight" name="weight"
                       value="{{ $orderPackage->weight }}">
            </div>
            <div class="form-group">
                <label for="quantity">@lang('order_packages.form.quantity')</label><br/>
                <input type="text" id="quantity" name="quantity" class="form-control"
                       value="{{ $orderPackage->quantity }}">
            </div>
            <div class="form-group">
                <label for="container_type">@lang('order_packages.form.container_type')</label><br/>
                <select class="form-control" id="container_type" name="container_type">
                    @foreach($containerTypes as $containerType)
                    @if ($containerType->name == $orderPackage->container_type)
                    <option value="{{$orderPackage->container_type}}" selected="selected">{{$orderPackage->container_type}}</option>
                    @else
                    <option value="{{ $containerType->name }}">{{ $containerType->name }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="packing_type">@lang('order_packages.form.packing_type')</label><br/>
                <select class="form-control" id="packing_type" name="packing_type">
                    @foreach($packingTypes as $packingType)
                    @if ($packingType->name == $orderPackage->container_type)
                    <option value="{{$orderPackage->container_type}}" selected="selected">{{$orderPackage->container_type}}</option>
                    @else
                    <option value="{{ $packingType->name }}">{{ $packingType->name }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="shape">@lang('order_packages.form.shape')</label><br/>
                <input type="text" id="shape" name="shape" class="form-control" value="{{ $orderPackage->shape }}">
            </div>
            <div class="form-group">
                <label for="cash_on_delivery">@lang('order_packages.form.cash_on_delivery')</label>
                <input type="text" class="form-control priceChange" id="cash_on_delivery" name="cash_on_delivery"
                       value="{{ $orderPackage->cash_on_delivery }}">
            </div>
            <div class="form-group">
                <label for="notices">@lang('order_packages.form.notices')</label>
                <textarea cols="40" rows="5" maxlength="40" type="text" class="form-control" id="notices"
                          name="notices">{{ $orderPackage->notices }}</textarea>
            </div>
            <div class="form-group">
                <label for="status">@lang('order_packages.form.status')</label>
                <select name="status" id="status" class="form-control">
                    @if($orderPackage->status == 'DELIVERED')
                        <option value="DELIVERED" selected>@lang('order_packages.form.status_type.delivered')</option>
                    @else
                        <option value="DELIVERED">@lang('order_packages.form.status_type.delivered')</option>
                    @endif
                    @if($orderPackage->status == 'CANCELLED')
                        <option value="CANCELLED" selected>@lang('order_packages.form.status_type.cancelled')</option>
                    @else
                        <option value="CANCELLED">@lang('order_packages.form.status_type.cancelled')</option>
                    @endif
                    @if($orderPackage->status == 'NEW')
                        <option value="NEW" selected>@lang('order_packages.form.status_type.new')</option>
                    @else
                        <option value="NEW">@lang('order_packages.form.status_type.new')</option>
                    @endif
                    @if($orderPackage->status == 'SENDING')
                        <option value="SENDING" selected>@lang('order_packages.form.status_type.sending')</option>
                    @else
                        <option value="SENDING">@lang('order_packages.form.status_type.sending')</option>
                    @endif
                    @if($orderPackage->status == 'WAITING_FOR_SENDING')
                        <option value="WAITING_FOR_SENDING"
                                selected>@lang('order_packages.form.status_type.waiting_for_sending')</option>
                    @else
                        <option value="WAITING_FOR_SENDING">@lang('order_packages.form.status_type.waiting_for_sending')</option>
                    @endif
                    @if($orderPackage->status == 'WAITING_FOR_CANCELLED')
                        <option value="WAITING_FOR_CANCELLED"
                                selected>@lang('order_packages.form.status_type.waiting_for_cancelled')</option>
                    @else
                        <option value="WAITING_FOR_CANCELLED">@lang('order_packages.form.status_type.waiting_for_cancelled')</option>
                    @endif
                    @if($orderPackage->status == 'REJECT_CANCELLED')
                        <option value="REJECT_CANCELLED"
                                selected>@lang('order_packages.form.status_type.reject_cancelled')</option>
                    @else
                        <option value="REJECT_CANCELLED">@lang('order_packages.form.status_type.reject_cancelled')</option>
                    @endif
                </select>
            </div>
            <div class="form-group">
                <label for="content">@lang('order_packages.form.content')</label>
                <select class="form-control text-uppercase" id="content" name="content">
                    @foreach($contentTypes as $contentType)
                    @if ($contentType->name == $orderPackage->content)
                    <option value="{{$orderPackage->content}}" selected="selected">{{$orderPackage->content}}</option>
                    @else
                    <option value="{{ $contentType->name }}">{{ $contentType->name }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="sending_number">@lang('order_packages.form.sending_number')</label>
                <input type="text" class="form-control" id="sending_number" name="sending_number"
                       value="{{ $orderPackage->sending_number }}">
            </div>
            <div class="form-group">
                <label for="letter_number">@lang('order_packages.form.letter_number')</label>
                <input type="text" class="form-control" id="letter_number" name="letter_number"
                       value="{{ $orderPackage->letter_number }}">
            </div>
            <div class="form-group">
                <label for="cost_for_client">@lang('order_packages.form.cost_for_client')</label>
                <input type="text" class="form-control priceChange" id="cost_for_client" name="cost_for_client"
                       value="{{ $orderPackage->cost_for_client }}">
            </div>
            <div class="form-group">
                <label for="cost_for_company">@lang('order_packages.form.cost_for_company')</label>
                <input type="text" class="form-control priceChange" id="cost_for_company" name="cost_for_company"
                       value="{{ $orderPackage->cost_for_company }}">
            </div>
            <div class="form-group">
                <label for="real_cost_for_company">@lang('order_packages.form.real_cost_for_company')</label>
                <input type="text" class="form-control priceChange" id="real_cost_for_company"
                       name="real_cost_for_company"
                       value="{{ $orderPackage->real_cost_for_company }}">
            </div>
            <input type="hidden" value="{{ $id }}" name="order_id">
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
        @if($orderPackage->status == 'WAITING_FOR_SENDING' || $orderPackage->status == 'SENDING')
            <a href="/admin/orderPackages/{{$id}}/sendRequestForCancelled"
               class="btn btn-danger">@lang('order_packages.form.cancelled_package')</a>
        @endif
    </form>
@endsection
@section('scripts')
    <script src="{{URL::asset('js/jscolor.js')}}"></script>
    <script>
      var breadcrumb = $('.breadcrumb:nth-child(2)');

      breadcrumb.children().remove();
      breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
      breadcrumb.append("<li class='active'><a href='/admin/orders/{{$orderPackage->order_id}}/edit'>Przesyłki</a></li>");
      breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");

      function commaReplace(cssClass) {
        document.querySelectorAll(cssClass).forEach(function (input) {
          input.value = input.value.replace(/,/g, '.');
        });
      }

      $(document).ready(function () {
        commaReplace('.priceChange');
      });

      $("#shipment_date").on('dp.change', function () {
        var shipmentDate = new Date($("#shipment_date").val());
        var deliveryDate = "", noOfDaysToAdd = 1, count = 0;

        while (count < noOfDaysToAdd) {
          deliveryDate = new Date(shipmentDate.setDate(shipmentDate.getDate() + 1));
          if (deliveryDate.getDay() != 0 && deliveryDate.getDay() != 6) {
            //Date.getDay() gives weekday starting from 0(Sunday) to 6(Saturday)
            count++;
          }
        }

        $("#delivery_date").val(moment(deliveryDate).format('YYYY-MM-DD'));

      });
    </script>
@endsection
