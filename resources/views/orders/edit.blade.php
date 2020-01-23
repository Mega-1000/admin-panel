@extends('layouts.datatable')
@section('app-header')
    <link rel="stylesheet" href="{{ URL::asset('css/views/orders/edit.css') }}">
    <h1 class="page-title" style="margin-right: 0px;">
        <i class="voyager-window-list"></i> @lang('orders.edit')
        <a style="margin-left: 15px;" href="{{ action('OrdersController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('orders.list')</span>
        </a>
    </h1>
    <a style="height: 36px; margin-bottom: 8px;"
       href="{{action('OrdersController@sendOfferToCustomer', ['id' => $order->id])}}"
       target="_blank"
       class="btn btn-primary">
        Wyślij ofertę do klienta
    </a>
    <button style="height: 36px; margin-bottom: 8px;" type="submit" form="orders" id="submitOrder" name="submit" value="update"
            class="btn btn-primary">@lang('voyager.generic.save')</button>
    <button style="height: 36px; margin-bottom: 8px;" type="submit" form="orders" id="submitOrderAndStay" name="submit" value="updateAndStay"
            class="btn btn-primary">@lang('voyager.generic.saveAndStay')</button>
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
    <div style="margin-bottom: 15px;" class="tab">
        <button class="btn btn-primary active"
                name="change-button-form" id="button-general"
                value="general">@lang('orders.form.buttons.details')</button>
        <button class="btn btn-primary"
                name="change-button-form" id="button-payments"
                value="payments">@lang('orders.form.buttons.payments')</button>
        <button class="btn btn-primary"
                name="change-button-form" id="button-tasks"
                value="tasks">@lang('orders.form.buttons.tasks')</button>
        <button class="btn btn-primary"
                name="change-button-form" id="button-messages"
                value="messages">@lang('orders.form.buttons.messages')</button>
        <button class="btn btn-primary"
                name="change-button-form" id="button-packages"
                value="packages">@lang('orders.form.buttons.packages')</button>
        <a id="create-button-orderPayments" style="float:right;margin-right: 15px;"
           href="{{route('order_payments.create', ['id' => $order->id]) }}" target="_blank" class="btn btn-success install pull-right">
            <i class="voyager-plus"></i> <span>@lang('order_payments.create')</span>
        </a>
        <a id="create-button-orderTasks" style="float:right;margin-right: 15px;"
           href="{{route('order_tasks.create', ['id' => $order->id]) }}" target="_blank" class="btn btn-success install pull-right">
            <i class="voyager-plus"></i> <span>@lang('order_tasks.create')</span>
        </a>
        <a id="create-button-orderPackages"
           href="{{route('order_packages.create', ['id' => $order->id]) }}" target="_blank" class="btn btn-success">
            <i class="voyager-plus"></i> <span>@lang('order_packages.create')</span>
        </a>
    </div>
    <form id="orders" action="{{ action('OrdersController@update', ['id' => $order->id])}}"
          method="POST">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="orders-general" id="general">
            <input type="hidden" value="{{Session::get('uri')}}" id="uri">
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="value_of_items_gross">@lang('orders.form.order_id')</label>
                <input type="text" class="form-control" id="order_id" name="order_id"
                       value="{{ $order->id }}" disabled>
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="employee">@lang('orders.form.employees')</label>
                <select name="employee" id="employee" class="form-control">
                    @if($order->employee_id == null)
                        <option value="none" selected>Brak pracownika</option>
                    @else
                        <option value="none">Brak pracownika</option>
                    @endif
                    @foreach($users as $user)
                        @if($user->id == $order->employee_id)
                            <option value="{{ $user->id }}" selected>{{ $user->name }} - {{ $user->firstname }} {{ $user->lastname }}</option>
                        @else
                            <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->firstname }} {{ $user->lastname }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="customer_address.firstname">@lang('customers.table.firstname')</label>
                <input type="text" class="form-control" id="customer_address.firstname" name="customer_address.firstname"
                       value="{{ $customerInfo->firstname ?? '' }}" disabled>
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="customer_address.lastname">@lang('customers.table.lastname')</label>
                <input type="text" class="form-control" id="customer_address.firstname" name="customer_address.lastname"
                       value="{{ $customerInfo->lastname ?? '' }}" disabled>
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="customer_address.phone">@lang('customers.table.phone')</label>
                <input type="text" class="form-control" id="customer_address.phone" name="customer_address.phone"
                       value="{{ $customerInfo->phone ?? '' }}" disabled>
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="document_number">@lang('orders.form.document_number')</label>
                <input class="form-control" id="document_number"
                       name="document_number"
                       value="{{ $order->document_number }}">
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="warehouse_cost">@lang('orders.form.warehouse_cost')</label>
                <input type="text" class="form-control priceChange" id="warehouse_cost" name="warehouse_cost"
                       value="{{ $order->warehouse_cost }}">
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="correction_amount">@lang('orders.form.correction_amount')</label>
                <input type="text" class="form-control priceChange" id="correction_amount" name="correction_amount"
                       value="{{ $order->correction_amount }}">
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="correction_description">@lang('orders.form.correction_description')</label>
                <input type="text" class="form-control" id="correction_description" name="correction_description"
                       value="{{ $order->correction_description }}">
            </div>
            <div class="form-group" style="width: 30%; float: left; padding: 5px;">
                <label for="status">@lang('orders.form.status')</label>
                <select name="status" id="status" class="form-control">
                    @foreach($statuses as $status)
                        <option {{$order->status_id === $status->id ? 'selected="selected"' : ''}} value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-check" style="width: 10%; margin-top: 10px; float: left; padding: 5px;">
                <input type="checkbox" class="form-check-input" id="shouldBeSent" name="shouldBeSent">
                <label class="form-check-label" for="shouldBeSent">Wysyłka maila</label>
            </div>
            <div class="form-group" style="width: 65%; float: left; padding: 5px;">
                <label for="status">@lang('orders.form.last_status_update_date')</label> <br/>
                {{$order->last_status_update_date}}
            </div>
            <div class="form-group" style="width: 10%; float: left; padding: 5px;">
                <label for="profitInfo">@lang('orders.form.profit')</label>
                <input type="text" class="form-control priceChange" id="profitInfo" disabled
                       value="">
            </div>
            <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                <label for="totalPriceInfo">Suma zamówienia</label>
                <input type="text" class="form-control" id="orderValueSum" name="orderValueSum"
                       value="" disabled>
            </div>
            <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                <label for="value_of_items_gross">@lang('orders.form.value_of_items_gross')</label>
                <input type="text" class="form-control priceChange sumChange" id="totalPriceInfo" disabled="" name="totalPriceInfo">

            </div>
            <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                <label for="additional_service_cost">@lang('orders.form.additional_service_cost')</label>
                <input type="text" class="form-control priceChange sumChange" id="additional_service_cost" name="additional_service_cost"
                       value="{{ $order->additional_service_cost }}">
            </div>
            <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                <label for="packing_warehouse_cost">@lang('orders.form.packing_warehouse_cost')</label>
                <input class="form-control priceChange sumChange" id="additional_cash_on_delivery_cost"
                       name="additional_cash_on_delivery_cost"
                       value="{{ $order->additional_cash_on_delivery_cost }}">
            </div>
            <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                <label for="shipment_price_for_client">@lang('orders.form.shipment_price_for_client')</label>
                <input type="text" class="form-control sumChange" id="shipment_price_for_client" name="shipment_price_for_client"
                       value="{{ $order->shipment_price_for_client ?? '' }}">
            </div>
            <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                <label for="shipment_price_for_us">@lang('orders.form.shipment_price_for_us')</label>
                <input type="text" class="form-control priceChange sumChange" id="shipment_price_for_us" name="shipment_price_for_us"
                       value="{{ $order->shipment_price_for_us ?? '' }}">
            </div>
            <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                <label for="proposed_payment">Proponowana zaliczka brutto</label>
                <input type="text" class="form-control priceChange" id="proposed_payment" value="{{ $order->proposed_payment ?? 500 }}" name="proposed_payment">
            </div>
            <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                <label for="payments">Zaliczka zaksięgowana brutto</label>
                <input type="text" class="form-control priceChange" id="payments" value="{{ $order->bookedPayments()->sum('amount') }}" name="payments" disabled>
            </div>
            <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                <label for="left_to_pay_on_delivery">Pozostało do zapłaty przed rozład.</label>
                <input type="text" class="form-control priceChange" id="left_to_pay_on_delivery" value="" name="left_to_pay_on_delivery" disabled>
            </div>
            <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                <label for="delivery_warehouse">@lang('orders.form.delivery_warehouse')</label>
                <input type="text" class="form-control" id="delivery_warehouse" name="delivery_warehouse"
                       value="{{ $warehouse->symbol ?? '' }}">
            </div>
            <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                <label for="weightInfo">Waga</label>
                <input type="text" class="form-control" id="weightInfo" disabled="" name="weightInfo">
            </div>
            <div class="form-group" style="width: 20%; float: left; padding: 5px;">
                <label for="production_date">@lang('orders.form.production_date')</label>
                <input type="text" class="form-control default-date-time-picker-now" id="production_date" name="production_date"
                       value="{{ $order->production_date }}">
            </div>
            <div class="form-group" style="width: 20%; float: left; padding: 5px;">
                <label for="shipment_date">@lang('orders.form.shipment_date')</label>
                <input type="text" class="form-control default-date-picker-now" id="shipment_date" name="shipment_date"
                       value="{{ $order->shipment_date }}">
            </div>
            <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                <label for="allegro_transaction_id">@lang('orders.form.allegro_transaction_id')</label>
                <input type="text" class="form-control" id="allegro_transaction_id" name="allegro_transaction_id"
                       value="{{ $order->allegro_transaction_id ?? '' }}">
            </div>
            @if($order->cash_on_dalivery_amount > 0)
                <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                    <label for="packing_warehouse_cost">@lang('orders.form.cash_on_delivery')</label>
                    <input type="text" class="form-control" id="cash_on_delivery" name="cash_on_delivery"
                           value="@lang('orders.form.true')" disabled>
                </div>
            @else
                <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                    <label for="packing_warehouse_cost">@lang('orders.form.cash_on_delivery')</label>
                    <input type="text" class="form-control" id="cash_on_delivery" name="cash_on_delivery"
                           value="@lang('orders.form.false')" disabled>
                </div>
            @endif
            <div class="form-group" style="width: 15%; float: left; padding: 5px;">
                <label for="customer.nick_allegro">@lang('orders.form.nick_allegro')</label>
                <input type="text" class="form-control" id="customer.nick_allegro" name="customer.nick_allegro"
                       value="{{ $order->customer->nick_allegro ?? ''}}" disabled>
            </div>
            <div class="form-group" style="width: 25%; float: left; padding: 5px;">
                <label for="warehouse_value">@lang('orders.form.warehouse_value')</label>
                <input type="number" class="form-control" id="warehouse_value" name="warehouse_value"
                       value="{{ $order->warehouse_value ?? ''}}">
            </div>
            <div class="form-group" style="width: 25%; float: left; padding: 5px;">
                <label for="consultant_value">@lang('orders.form.consultant_value')</label>
                <input type="number" class="form-control" id="consultant_value" name="consultant_value"
                       value="{{ $order->consultant_value ?? ''}}">
            </div>
            <div class="form-group" style="width: 50%; float: left; padding: 5px;">
                <label for="warehouse_notice">@lang('orders.form.warehouse_notice')</label>
                <textarea rows="5" cols="40"  class="form-control" id="warehouse_notice" name="warehouse_notice">{{ $order->warehouse_notice ?? ''}}</textarea>
            </div>
            <div class="form-group" style="width: 50%; float: left; padding: 5px;">
                <label for="warehouse_notice">Informacje dla spedycji</label>
                <textarea rows="5" cols="40"  class="form-control" id="spedition_comment" name="spedition_comment">{{ $order->spedition_comment ?? ''}}</textarea>
            </div>
            <div class="form-group" style="width: 50%; float: left; padding: 5px;">
                <label for="consultant_notice">@lang('orders.form.consultant_notice')</label>
                <textarea rows="5" cols="40" type="text" class="form-control" id="consultant_notice" name="consultant_notice">{{ $order->consultant_notice ?? ''}}</textarea>
            </div>
            <div class="form-group" style="width: 60%; float: left; padding: 5px;">
                <label for="consultant_notices">@lang('orders.form.consultant_notices')</label>
                <textarea class="form-control" name="consultant_notices" id="consultant_notices" rows="5">{{ $order->consultant_notices ?? ''}}</textarea>
            </div>
            <div class="form-group" style="width: 40%; float: left; padding: 5px;">
                <label for="remainder_date">@lang('orders.form.remainder_date')</label>
                <input type="text" class="form-control default-date-time-picker-now" id="remainder_date" name="remainder_date" value="{{ $order->remainder_date }}">
            </div>
            <div class="form-group" style="width: 40%; padding: 5px;">
                <a href="/admin/orders/{{$order->id}}/getDataFromLastOrder" class="btn btn-success">Pobierz dane z ostatniego zamówienia</a>
                <a href="/admin/orders/{{$order->id}}/getDataFromCustomer" class="btn btn-success">Pobierz dane klienta</a>
                <input type="text" class="form-control" id="firms_data" name="firms_data"
                       value="MEGA-OLAWA">
                <button type="button" class="btn btn-success" onclick="getFirmData({{$order->id}})">Pobierz dane firmy</button>

            </div>
            <h3 style="float: left; width: 100%;">Dane do wysyłki</h3>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="order_delivery_address_firstname">@lang('customers.form.delivery_firstname')</label>
                <input type="text" class="form-control" id="order_delivery_address_firstname" name="order_delivery_address_firstname"
                       value="{{ $orderDeliveryAddress->firstname ?? ''}}">
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="order_delivery_address_lastname">@lang('customers.form.delivery_lastname')</label>
                <input type="text" class="form-control" id="order_delivery_address_lastname" name="order_delivery_address_lastname"
                       value="{{ $orderDeliveryAddress->lastname ?? ''}}">
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="order_delivery_address_email">@lang('customers.form.delivery_email')</label>
                <input type="email" class="form-control" id="order_delivery_address_email" name="order_delivery_address_email"
                       value="{{ $orderDeliveryAddress->email ?? ''}}">
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="order_delivery_address_firmname">@lang('customers.form.delivery_firmname')</label>
                <input type="text" class="form-control" id="order_delivery_address_firmname" name="order_delivery_address_firmname"
                       value="{{ $orderDeliveryAddress->firmname ?? ''}}">
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="order_delivery_address_address">@lang('customers.form.delivery_address')</label>
                <input type="text" class="form-control" id="order_delivery_address_address" name="order_delivery_address_address"
                       value="{{ $orderDeliveryAddress->address ?? ''}}">
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="order_delivery_address_flat_number">@lang('customers.form.delivery_flat_number')</label>
                <input type="text" class="form-control" id="order_delivery_address_flat_number" name="order_delivery_address_flat_number"
                       value="{{ $orderDeliveryAddress->flat_number ?? ''}}">
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="order_delivery_address_city">@lang('customers.form.delivery_city')</label>
                <input type="text" class="form-control" id="order_delivery_address_city" name="order_delivery_address_city"
                       value="{{ $orderDeliveryAddress->city ?? ''}}">
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="order_delivery_address_postal_code">@lang('customers.form.delivery_postal_code')</label>
                <input type="text" class="form-control" id="order_delivery_address_postal_code" name="order_delivery_address_postal_code"
                       value="{{ $orderDeliveryAddress->postal_code ?? ''}}">
            </div>
            <div class="form-group" style="width: 11%; float: left; padding: 5px;">
                <label for="order_delivery_address_phone">@lang('customers.form.delivery_phone')</label>
                <input type="text" class="form-control" id="order_delivery_address_phone" name="order_delivery_address_phone"
                       value="{{ $orderDeliveryAddress->phone ?? ''}}">
            </div>
            <h3 style="float: left; width: 100%;">Dane do faktury</h3>
            <div class="form-group" style="width: 10%; float: left; padding: 5px;">
                <label for="order_invoice_address_firstname">@lang('customers.form.invoice_firstname')</label>
                <input type="text" class="form-control" id="order_invoice_address_firstname" name="order_invoice_address_firstname"
                       value="{{ $orderInvoiceAddress->firstname ?? ''}}">
            </div>
            <div class="form-group" style="width: 10%; float: left; padding: 5px;">
                <label for="order_invoice_address_lastname">@lang('customers.form.invoice_lastname')</label>
                <input type="text" class="form-control" id="order_invoice_address_lastname" name="order_invoice_address_lastname"
                       value="{{ $orderInvoiceAddress->lastname ?? ''}}">
            </div>
            <div class="form-group" style="width: 10%; float: left; padding: 5px;">
                <label for="order_invoice_address_email">@lang('customers.form.invoice_email')</label>
                <input type="email" class="form-control" id="order_invoice_address_email" name="order_invoice_address_email"
                       value="{{ $orderInvoiceAddress->email ?? ''}}">
            </div>
            <div class="form-group" style="width: 10%; float: left; padding: 5px;">
                <label for="order_invoice_address_firmname">@lang('customers.form.invoice_firmname')</label>
                <input type="text" class="form-control" id="order_invoice_address_firmname" name="order_invoice_address_firmname"
                       value="{{ $orderInvoiceAddress->firmname ?? ''}}">
            </div>
            <div class="form-group" style="width: 10%; float: left; padding: 5px;">
                <label for="order_invoice_address_address">@lang('customers.form.invoice_address')</label>
                <input type="text" class="form-control" id="order_invoice_address_address" name="order_invoice_address_address"
                       value="{{ $orderInvoiceAddress->address ?? ''}}">
            </div>
            <div class="form-group" style="width: 10%; float: left; padding: 5px;">
                <label for="order_invoice_address_flat_number">@lang('customers.form.invoice_flat_number')</label>
                <input type="text" class="form-control" id="order_invoice_address_flat_number" name="order_invoice_address_flat_number"
                       value="{{ $orderInvoiceAddress->flat_number ?? ''}}">
            </div>
            <div class="form-group" style="width: 10%; float: left; padding: 5px;">
                <label for="order_invoice_address_city">@lang('customers.form.invoice_city')</label>
                <input type="text" class="form-control" id="order_invoice_address_city" name="order_invoice_address_city"
                       value="{{ $orderInvoiceAddress->city ?? ''}}">
            </div>
            <div class="form-group" style="width: 10%; float: left; padding: 5px;">
                <label for="order_invoice_address_postal_code">@lang('customers.form.invoice_postal_code')</label>
                <input type="text" class="form-control" id="order_invoice_address_postal_code" name="order_invoice_address_postal_code"
                       value="{{ $orderInvoiceAddress->postal_code ?? ''}}">
            </div>
            <div class="form-group" style="width: 10%; float: left; padding: 5px;">
                <label for="order_invoice_address_phone">@lang('customers.form.invoice_phone')</label>
                <input type="text" class="form-control" id="order_invoice_address_phone" name="order_invoice_address_phone"
                       value="{{ $orderInvoiceAddress->phone ?? ''}}">
            </div>
            <div class="form-group" style="width: 10%; float: left; padding: 5px;">
                <label for="order_invoice_address_nip">@lang('customers.form.invoice_nip')</label>
                <input type="text" class="form-control" id="order_invoice_address_nip" name="order_invoice_address_nip"
                       value="{{ $orderInvoiceAddress->nip ?? ''}}">
            </div>
            <input type="hidden" value="{{ $order->customer->id }}" name="customer_id">
            <div class="form-group" style="widht: 100%; float: left;">
                <a target="_blank" href="{{env('FRONT_URL')}}?order_token={{$order->getToken()}}" for="add-item">
                    Edytuj zamówienie w koszyku
                </a>
                <br>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                    Podziel zamówienie
                </button>
            </div>
            <h3 style="clear: both;">Produkty</h3>
            <table id="productsTable" class="table table1 table-venice-blue productsTableEdit">
                <tbody id="products-tbody">
                @php
                    $gross_purchase_sum = 0;
                    $net_purchase_sum = 0;
                    $gross_selling_sum = 0;
                    $net_selling_sum = 0;
                    $weight = 0;
                @endphp
                @foreach($order->items as $item)
                    @php
                        $gross_purchase_sum += ($item->net_purchase_price_commercial_unit * $item->quantity * 1.23);
                        $net_purchase_sum += $item->net_purchase_price_commercial_unit * $item->quantity ;
                        $gross_selling_sum += ($item->net_selling_price_commercial_unit * $item->quantity * 1.23);
                        $net_selling_sum += $item->net_selling_price_commercial_unit * $item->quantity;
                        $weight += $item->product->weight_trade_unit * $item->quantity;
                    @endphp
                    <tr class="id row-{{$item->id}}" id="id[{{$item->id}}]">
                        <td colspan="4"><h4><img src="{!! $item->product->url_for_website !!}" style="width: 179px; height: 130px;"><strong>{{ $loop->iteration }}. </strong>{{ $item->product->name }} (symbol: {{ $item->product->symbol }}) </h4></td>

                        <input name="id[{{$item->id}}]"
                               value="{{ $item->id }}" type="hidden"
                               class="form-control" id="id[{{$item->id}}]">
                        <input name="product_id[{{$item->id}}]"
                               value="{{ $item->product_id }}" type="hidden"
                               class="form-control" id="product_id[{{$item->id}}]">

                        <input
                                value="{{ $item->quantity }}" type="hidden"
                                class="form-control item_quantity"  name="item_quantity[{{$item->id}}]" data-item-id="{{$item->id}}">

                        <input name="numbers_of_basic_commercial_units_in_pack[{{$item->id}}]"
                               data-item-id="{{$item->id}}" value="{{ $item->product->packing->numbers_of_basic_commercial_units_in_pack }}" type="hidden"
                               class="form-control numbers_of_basic_commercial_units_in_pack" id="numbers_of_basic_commercial_units_in_pack[{{$item->id}}]">
                        <input name="number_of_sale_units_in_the_pack[{{$item->id}}]"
                               data-item-id="{{$item->id}}" value="{{ $item->product->packing->number_of_sale_units_in_the_pack }}" type="hidden"
                               class="form-control number_of_sale_units_in_the_pack" id="number_of_sale_units_in_the_pack[{{$item->id}}]">
                        <input name="number_of_trade_items_in_the_largest_unit[{{$item->id}}]"
                               data-item-id="{{$item->id}}" value="{{ $item->product->packing->number_of_trade_items_in_the_largest_unit }}" type="hidden"
                               class="form-control number_of_trade_items_in_the_largest_unit" id="number_of_trade_items_in_the_largest_unit[{{$item->id}}]">
                        <input name="unit_consumption[{{$item->id}}]"
                               data-item-id="{{$item->id}}" value="{{ $item->product->packing->unit_consumption }}" type="hidden"
                               class="form-control unit_consumption" id="unit_consumption[{{$item->id}}]">
                    </tr>
                    <tr>
                        <td colspan="4">
                            Obliczenia dokonano przy założeniu  {{$item->product->packing->unit_consumption}}  {{$item->product->packing->unit_basic}} / {{$item->product->packing->calculation_unit}}
                            <br> 1 {{$item->product->packing->unit_of_collective}} = {{$item->product->packing->number_of_sale_units_in_the_pack}} ({{$item->product->packing->unit_commercial}}) = @if(is_numeric($item->product->packing->number_of_sale_units_in_the_pack) && is_numeric($item->product->packing->unit_consumption)){{$item->product->packing->number_of_sale_units_in_the_pack / $item->product->packing->unit_consumption}} @else {{0}} @endif ({{$item->product->packing->calculation_unit}} / {{$item->product->packing->unit_of_collective}})
                        </td>
                    </tr>
                    <tr>
                        <td>Jednostka handlowa ({{ $item->product->packing->unit_commercial }})</td>
                        <td>Jednostka podstawowa ({{$item->product->packing->unit_basic}})</td>
                        <td>Jednostka obliczeniowa ({{$item->product->packing->calculation_unit}})</td>
                        <td>Jednostka zbiorcza ({{$item->product->packing->unit_of_collective}})</td>
                    </tr>
                    <tr class="row-{{$item->id}}">
                        <th colspan="4">Cena sprzedaży netto</th>
                    </tr>
                    <tr class="selling-row row-{{$item->id}}">
                        <td>
                            <input name="net_selling_price_commercial_unit[{{$item->id}}]"
                                   data-item-id="{{$item->id}}" value="{{ $item->net_selling_price_commercial_unit }}" type="text"
                                   class="form-control price net_selling_price_commercial_unit priceChange change-order" id="net_selling_price_commercial_unit[{{$item->id}}]">
                        </td>
                        <td>
                            <input name="net_selling_price_basic_unit[{{$item->id}}]"
                                   data-item-id="{{$item->id}}" value="{{ $item->net_selling_price_basic_unit }}" type="text"
                                   class="form-control price net_selling_price_basic_unit priceChange change-order" id="net_selling_price_basic_unit[{{$item->id}}]">
                        </td>
                        <td>
                            <input name="net_selling_price_calculated_unit[{{$item->id}}]"
                                   data-item-id="{{$item->id}}" value="{{ $item->net_selling_price_calculated_unit }}" type="text"
                                   class="form-control price net_selling_price_calculated_unit priceChange change-order" id="net_selling_price_calculated_unit[{{$item->id}}]">
                        </td>
                        <td>
                            <input name="net_selling_price_aggregate_unit[{{$item->id}}]"
                                   data-item-id="{{$item->id}}" value="{{ $item->net_selling_price_aggregate_unit }}" type="text"
                                   class="form-control price net_selling_price_aggregate_unit priceChange change-order" id="net_selling_price_aggregate_unit[{{$item->id}}]">
                        </td>
                    </tr>
                    <tr class="row-{{$item->id}}">
                        <th colspan="4">Cena sprzedaży brutto</th>
                    </tr>
                    <tr class="selling-row row-{{$item->id}}">
                        <td>
                            <input name="gross_selling_price_commercial_unit[{{$item->id}}]"
                                   data-item-id="{{$item->id}}" value="{{$item->gross_selling_price_commercial_unit}}" type="text"
                                   class="form-control price gross_selling_price_commercial_unit priceChange change-order" id="gross_selling_price_commercial_unit[{{$item->id}}]" >
                        </td>
                        <td>
                            <input name="gross_selling_price_basic_unit[{{$item->id}}]"
                                   data-item-id="{{$item->id}}" value="{{$item->gross_selling_price_basic_unit}}" type="text"
                                   class="form-control price gross_selling_price_basic_unit priceChange change-order" id="gross_selling_price_basic_unit[{{$item->id}}]" >
                        </td>
                        <td>
                            <input name="gross_selling_price_calculated_unit[{{$item->id}}]"
                                   data-item-id="{{$item->id}}" value="{{$item->gross_selling_price_calculated_unit}}" type="text"
                                   class="form-control price gross_selling_price_calculated_unit priceChange change-order" id="gross_selling_price_calculated_unit[{{$item->id}}]" >
                        </td>
                        <td>
                            <input name="gross_selling_price_aggregate_unit[{{$item->id}}]"
                                   data-item-id="{{$item->id}}" value="{{$item->gross_selling_price_aggregate_unit}}" type="text"
                                   class="form-control price gross_selling_price_aggregate_unit priceChange change-order" id="gross_selling_price_aggregate_unit[{{$item->id}}]" >
                        </td>
                    </tr>
                    <tr class="row-{{$item->id}}">
                        <th colspan="4">Cena zakupu po rabatach netto</th>
                    </tr>
                    <tr class="purchase-row row-{{$item->id}}">
                        <td>
                            <input name="net_purchase_price_commercial_unit[{{$item->id}}]"
                                   data-item-id="{{$item->id}}" value="{{ $item->net_purchase_price_commercial_unit }}" type="text"
                                   class="form-control price net_purchase_price_commercial_unit priceChange" id="net_purchase_price_commercial_unit[{{$item->id}}]">
                        </td>
                        <td>
                            <input name="net_purchase_price_basic_unit[{{$item->id}}]"
                                   data-item-id="{{$item->id}}" value="{{ $item->net_purchase_price_basic_unit }}" type="text"
                                   class="form-control price net_purchase_price_basic_unit priceChange" id="net_purchase_price_basic_unit[{{$item->id}}]">
                        </td>
                        <td>
                            <input name="net_purchase_price_calculated_unit[{{$item->id}}]"
                                   data-item-id="{{$item->id}}" value="{{ $item->net_purchase_price_calculated_unit }}" type="text"
                                   class="form-control price net_purchase_price_calculated_unit priceChange" id="net_purchase_price_calculated_unit[{{$item->id}}]">
                        </td>
                        <td>
                            <input name="net_purchase_price_aggregate_unit[{{$item->id}}]"
                                   data-item-id="{{$item->id}}" value="{{ $item->net_purchase_price_aggregate_unit }}" type="text"
                                   class="form-control price net_purchase_price_aggregate_unit priceChange" id="net_purchase_price_aggregate_unit[{{$item->id}}]">
                        </td>
                    </tr>
                    <tr class="row-{{$item->id}}">
                        <th colspan="4">Cena zakupu po rabatach brutto</th>
                    </tr>
                    <tr class="purchase-row row-{{$item->id}}">
                        <td>
                            <input name="gross_purchase_price_commercial_unit[{{$item->id}}]"
                                   value="{{$item->gross_purchase_price_commercial_unit}}" type="text"
                                   data-item-id="{{$item->id}}" class="form-control price gross_purchase_price_commercial_unit priceChange" id="gross_purchase_price_commercial_unit[{{$item->id}}]" >
                        </td>
                        <td>
                            <input name="gross_purchase_price_basic_unit[{{$item->id}}]"
                                   value="{{$item->gross_purchase_price_basic_unit}}" type="text"
                                   data-item-id="{{$item->id}}" class="form-control price gross_purchase_price_basic_unit priceChange" id="gross_purchase_price_basic_unit[{{$item->id}}]" >
                        </td>
                        <td>
                            <input name="gross_purchase_price_calculated_unit[{{$item->id}}]"
                                   value="{{$item->gross_purchase_price_calculated_unit}}" type="text"
                                   data-item-id="{{$item->id}}" class="form-control price gross_purchase_price_calculated_unit priceChange" id="gross_purchase_price_calculated_unit[{{$item->id}}]" >
                        </td>
                        <td>
                            <input name="gross_purchase_price_aggregate_unit[{{$item->id}}]"
                                   value="{{$item->gross_purchase_price_aggregate_unit}}" type="text"
                                   data-item-id="{{$item->id}}" class="form-control price gross_purchase_price_aggregate_unit priceChange" id="gross_purchase_price_aggregate_unit[{{$item->id}}]" >
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6">
                            Wagi produktu
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input value="{{ $item->product->weight_trade_unit }} kg" type="text"
                                   class="form-control price net_purchase_price_commercial_unit priceChange" name="weight_trade_unit[{{$item->id}}]" disabled>
                            <input type="hidden" name="weight_trade_unit[{{$item->id}}]" value="{{ $item->product->weight_trade_unit }}">
                        </td>
                        <td>
                            @if($item->product->numbers_of_basic_commercial_units_in_pack == 0)
                                <input value="0 kg" type="text"
                                       class="form-control price net_purchase_price_commercial_unit priceChange" disabled>
                            @else
                                <input value="{{ $item->product->weight_trade_unit / $item->product->numbers_of_basic_commercial_units_in_pack  }} kg" type="text"
                                       class="form-control price net_purchase_price_commercial_unit priceChange" disabled>
                            @endif
                        </td>
                        <td>
                            @if($item->product->numbers_of_basic_commercial_units_in_pack == 0)
                                <input value="0 kg" type="text"
                                       class="form-control price net_purchase_price_commercial_unit priceChange" disabled>
                            @else
                                <input value="{{ ($item->product->packing->number_of_sale_units_in_the_pack / $item->product->numbers_of_basic_commercial_units_in_pack ?? 1) * $item->product->unit_consumption }} kg" type="text"
                                       class="form-control price net_purchase_price_commercial_unit priceChange" disabled>
                            @endif
                        </td>
                        <td>
                            <input value="{{ $item->product->packing->number_of_sale_units_in_the_pack * $item->product->number_of_sale_units_in_the_pack }} kg" type="text"
                                   class="form-control price net_purchase_price_commercial_unit priceChange" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            Zamawiana ilość
                        </td>
                    </tr>
                    <tr class="selling-row row-{{$item->id}}">
                        @foreach($productPacking as $packing)
                            @if($packing->product_id === $item->product_id)
                                <td>
                                    <input name="unit_commercial[{{$item->id}}]"
                                           value="{{$item->quantity . ' ' . $packing->unit_commercial }}" type="text"
                                           class="form-control" id="unit_commercial" disabled>
                                    <input type="hidden" name="unit_commercial_quantity[{{$item->id}}]" value="{{ $item->quantity }}">
                                    <input type="hidden" name="unit_commercial_name[{{$item->id}}]" value="{{ $packing->unit_commercial }}">
                                </td>
                                <td>
                                    <input name="unit_basic"
                                           value="@if($item->product->packing->numbers_of_basic_commercial_units_in_pack != 0){{$item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack  .' '.$packing->unit_basic }} @else {{0}} @endif" type="text"
                                           class="form-control" id="unit_basic" disabled>
                                    <input type="hidden" name="unit_basic_units[{{$item->id}}]" value="{{ $item->product->packing->numbers_of_basic_commercial_units_in_pack}}">
                                    <input type="hidden" name="unit_basic_name[{{$item->id}}]" value="{{ $packing->unit_basic }}">
                                </td>
                                <td>
                                    <input name="calculation_unit[{{$item->id}}]"
                                           value="@if(is_numeric($item->product->packing->numbers_of_basic_commercial_units_in_pack) && is_numeric($item->product->packing->unit_consumption)){{ number_format($item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack / $item->product->packing->unit_consumption, 2) .' '.$packing->calculation_unit }} @else {{0}} @endif" type="text"
                                           class="form-control" id="calculation_unit" disabled>
                                    <input type="hidden" name="calculation_unit_units[{{$item->id}}]" value="{{ $item->product->packing->numbers_of_basic_commercial_units_in_pack}}">
                                    <input type="hidden" name="calculation_unit_consumption[{{$item->id}}]" value="{{ $item->product->packing->unit_consumption }}">
                                    <input type="hidden" name="calculation_unit_name[{{$item->id}}]" value="{{ $packing->calculation_unit }}">
                                </td>
                                <td>
                                    @php
                                        if ($item->product->packing->number_of_sale_units_in_the_pack == 0)
                                            $a = 0;
                                        else
                                            $a = $item->quantity / $item->product->packing->number_of_sale_units_in_the_pack;
                                    @endphp
                                    <input name="unit_of_collective[{{$item->id}}]"
                                           value="{{ number_format($a, 4) .' '.$packing->unit_of_collective}} " type="text"
                                           class="form-control" id="unit_of_collective" disabled>
                                    <input type="hidden" name="unit_of_collective_units[{{$item->id}}]" value="{{ $item->product->packing->number_of_sale_units_in_the_pack }}">
                                    <input type="hidden" name="unit_of_collective_name[{{$item->id}}]" value="{{ $packing->unit_of_collective }}">
                                </td>
                            @endif
                        @endforeach
                    </tr>
                    <tr class="row-{{$item->id}}">
                        <th colspan="4">Ilość</th>
                    </tr>
                    <tr class="selling-row row-{{$item->id}}">
                        <td>
                            <input name="quantity_commercial[{{$item->id}}]"
                                   value="{{ $item->quantity }}" type="text" data-item-id="{{$item->id}}"
                                   class="form-control price change-order quantityChange" id="quantity_commercial[{{$item->id}}]">
                        </td>
                        <td colspan="3">
                            @php
                                $quantityAll = 0;
                            @endphp
                            @foreach($item->realProductPositions() as $position)
                                <p>Pozycja: {{ $position->lane }} {{ $position->bookstand }} {{ $position->shelf }} {{ $position->position }} Ilość na pozycji: {{ $position->position_quantity }}</p>
                                @php
                                    $quantityAll += $position->position_quantity;
                                @endphp
                            @endforeach
                                Ilość wszystkich: {{ $quantityAll }} <br/>
                        </td>
                    </tr>
                    <tr class="row-{{$item->id}}">
                        <th colspan="4">Wartość asortymentu</th>
                    </tr>
                    <tr class="selling-row row-{{$item->id}}">
                        <td>
                            <input type="text" class="form-control item-value priceChange" data-item-id="{{$item->id}}" disabled name="item-value"
                                   value="{{ number_format(($item->net_selling_price_commercial_unit * $item->quantity * 1.23), 2) }} zł">
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    <tr class="row-{{$item->id}}">
                        <th colspan="4">Waga asortymentu</th>
                    </tr>
                    <tr class="selling-row row-{{$item->id}}">
                        <td>
                            <input type="text" class="form-control item-weight priceChange" data-item-id="{{$item->id}}" disabled name="item-weight"
                                   value="{{ number_format(($item->product->weight_trade_unit * $item->quantity), 2) }} kg">
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    <tr class="row-{{$item->id}}">
                        <th colspan="4">Zysk danego asortymentu</th>
                    </tr>
                    <tr class="selling-row row-{{$item->id}}">
                        <td>
                            <input type="text" class="form-control item-profit priceChange" data-item-id="{{$item->id}}" disabled name="item-profit"
                                   value="{{ number_format(($item->net_selling_price_commercial_unit * $item->quantity * 1.23) - ($item->net_purchase_price_commercial_unit * $item->quantity * 1.23), 2) }} zł">
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    @if(!empty($productsVariation[$item->product->id]))
                        <tr>
                            <td colspan="4"><h3>Wariacje produktów:</h3></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Nazwa</strong>
                            </td>
                            <td>
                                <strong>Cena sprzedaży brutto jednostki handlowej</strong>
                            </td>
                            <td>
                                <strong>Cena sprzedaży brutto jednostki podstawowej</strong>
                            </td>
                            <td>
                                <strong>Cena sprzedaży brutto jednostki obliczeniowej</strong>
                            </td>
                            <td>
                                <strong>Wartość danego asortymentu</strong>
                            </td>
                            <td>
                                <strong>Różnica</strong>
                            </td>
                            <td>
                                <strong>Odległość</strong>
                            </td>
                        </tr>
                        @foreach($productsVariation[$item->product->id] as $variation)
                            <tr class="row-{{$variation['id']}}">
                                <td>
                                    {{$variation['name']}}
                                </td>
                                <td>
                                    {{$variation['gross_selling_price_commercial_unit']}}
                                </td>
                                <td>
                                    {{$variation['gross_selling_price_basic_unit']}}
                                </td>
                                <td>
                                    {{$variation['gross_selling_price_calculated_unit']}}
                                </td>
                                <td>
                                    {{$variation['sum']}}
                                </td>
                                <td>
                                        @if(strstr($variation['different'], '-') != false)
                                            <span style="color:red;">{{(float)$variation['different']}}</span>
                                        @else
                                            <span style="color:green;">+{{(float)$variation['different']}}</span>
                                        @endif
                                </td>
                                <td>
                                    {{(int)$variation['radius']}} km
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
                </tbody>
            </table>

            <div class="form-group">
                <input type="hidden" class="form-control" id="weight" name="weight"
                       value="{{ $order->weight ?? '' }}">
            </div>
            <div class="form-group">
                <input type="hidden" class="form-control" id="profit" disabled name="profit"
                       value="{{ number_format($gross_selling_sum - $gross_purchase_sum, 2) }}">
                <input type="hidden" class="form-control" id="orderItemsSum" disabled name="orderItemsSum"
                       value="{{ number_format($gross_selling_sum, 2) }}">
            </div>
            <div class="form-group">
                <input type="hidden" class="form-control priceChange" id="total_price" disabled name="total_price"
                       value="{{ $order->total_price ?? '' }}">
            </div>
            @if(!empty($allProductsFromSupplier))
                <h3>Suma wszystkich towarów dla danych producentów</h3>
                <table class="table table1 table-venice-blue productsTableEdit">
                    <thead>
                    <tr>
                        <th>Symbol dostawcy</th>
                        <th>Wartość całości towaru u dostawcy</th>
                        <th>Różnica wartości do wskazanego producenta w zamówieniu</th>
                        <th>Odległość od magazynu</th>
                        <th>Numer telefonu do przedstawiciela fabryki</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($allProductsFromSupplier as $productSupplier)
                        <tr>
                            <td>{{$productSupplier['product_name_supplier']}}</td>
                            <td>{{$productSupplier['sum']}}</td>
                            <td>
                                @if(strstr($productSupplier['different'], '-') != false)
                                    <span style="color:red;">{{(float)$productSupplier['different']}}</span>
                                @else
                                    <span style="color:green;">+{{(float)$productSupplier['different']}}</span>
                                @endif
                            </td>
                            <td>{{(int)$productSupplier['radius']}} km</td>
                            <td>{{$productSupplier['phone']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
            <div class="form-group">
                <label for="mail_message">@lang('orders.form.message')</label>
                <textarea cols="40" rows="50" style="height: 300px;" class="form-control editor" id="mail_message" name="mail_message"></textarea>
            </div>
            <h3>Dane klienta</h3>
            <div class="form-group">
                <label for="customer.login">@lang('orders.form.login')</label>
                <input type="text" class="form-control" id="customer.login" name="customer.login"
                       value="{{ $order->customer->login ?? ''}}" disabled>
            </div>

            <div class="form-group">
                <label for="customer_address.email">@lang('customers.table.email')</label>
                <input type="email" class="form-control" id="customer_address.email" name="customer_address.email"
                       value="{{ $orderDeliveryAddress->email ?? '' }}" disabled>
            </div>
            <h3>Etykiety</h3>
            @foreach($order->labels as $label)
                <span style="color: {{ $label->font_color }}; margin-top: 5px; background-color:{{ $label->color }}"><i style="font-size: 1rem" class="{{ $label->icon_name }}"></i> {{ $label->name }}</span>
            @endforeach
        </div>

        <button type="submit" form="orders" id="submit" name="submit" value="update"
                class="btn btn-primary">@lang('voyager.generic.save')</button>
        <button type="submit" form="orders" id="new-order" name="submit" value="store" class="btn btn-success">Dodaj nowe zamówienie</button>
    </form>
    <div class="order-payments" id="order-payments">
        @if(!empty($uri))
            <input id="uri" type="hidden" value="{{$uri}}">
        @endif
        <h3>Wpłaty dla zamówienia</h3>
        <table style="width: 100%" id="dataTableOrderPayments" class="table table-hover">
            <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>@lang('order_payments.table.order_id')</th>
                <th>@lang('order_payments.table.amount')</th>
                <th>@lang('order_payments.table.notices')</th>
                <th>@lang('order_payments.table.promise')</th>
                <th>@lang('order_payments.table.promise_date')</th>
                <th>@lang('order_payments.table.created_at')</th>
                <th>@lang('order_payments.table.actions')</th>
            </tr>
            </thead>
        </table>
        <h1>Rozrachunki z kontrahentem: {{ $order->customer->login }}
            @if(Auth::user()->role_id == 3 || Auth::user()->role_id == 2 || Auth::user()->role_id == 1)
            <a id="create-button-orderPayments" style="float:right;margin-right: 15px;"  href="{{route('order_payments.createMaster', ['id' => $order->id]) }}" class="btn btn-success install pull-right">
                <i class="voyager-plus"></i> <span>@lang('order_payments.createMaster')</span>
            </a>
            @endif
        </h1>
        <table style="width: 50%; float: left;" id="paymentsTable" class="table table-hover">
            <thead>
            <tr>
                <th>@lang('order_payments.table.payments')</th>
                <th>@lang('order_payments.table.booked_date')</th>
                <th>@lang('order_payments.table.title')</th>
                <th>@lang('order_payments.table.booked_orders')</th>
                <th>@lang('order_payments.table.payment_left')</th>
                <th>Zaliczka</th>
                <th>@lang('order_payments.table.add')</th>
            </tr>
            </thead>
            <tbody>
                @php
                    $sumOfPayments = 0;
                @endphp
                @foreach($order->customer->payments as $payment)
                    @php
                        $sumOfPayments = $sumOfPayments + $payment->amount;
                    @endphp
                    <tr>
                        <td>{{ $payment->amount }}</td>
                        <td>{{ $payment->created_at }}</td>
                        <td>{{ $payment->title }}</td>
                        <td>
                            @foreach($payment->getOrdersUsingPayment() as $orderId => $paymentsValue)
                                <b style="font-weight: 700;">{{ $orderId }}</b> - {{ $paymentsValue }} zł <br/>
                            @endforeach
                        </td>
                        <td>{{ $payment->amount_left }}</td>
                        <td>
                            @if($payment->promise == '1')
                                <b style="color: red;">Tak</b>
                            @else
                                <b style="color: red;">Nie</b>
                            @endif
                        </td>
                        <td>
                            @if($payment->promise == '1' && Auth::user()->role_id != 4)
                                <button type="button" class="btn btn-success openPromiseModal" style="display: block;" data-payment="{{ $payment->id }}" data-payment-amount="{{ $payment->amount }}">
                                    Zaksięguj
                                </button>
                            @else
                                <button type="button" class="btn" style="display: block;" disabled>
                                    Zaksięgowano
                                </button>
                                <button type="button" class="btn btn-primary openPaymentModal" style="display: block;" data-payment="{{ $payment->id }}" data-payment-amount="{{ $payment->amount }}">
                                    Przydziel
                                </button>
                            @endif
                            <a href="{{ route('payments.edit', ['id' => $payment->id]) }}" class="btn btn-info">Edytuj</a>
                            <a href="{{ route('payments.destroy', ['id' => $payment->id]) }}" class="btn btn-danger">Usuń</a>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td><h2>Suma wpłat: <b style="color: red;">{{ $sumOfPayments }} zł</b></h2></td>
                </tr>
            </tbody>
        </table>
        <table style="width: 49%; margin-left: 1%; display: inline-block;" id="ordersTable" class="table table-hover">
            <thead>
            <tr>
                <th>@lang('order_payments.table.order_id')</th>
                <th>@lang('order_payments.table.order_status')</th>
                <th>@lang('order_payments.table.order_value')</th>
                <th>@lang('order_payments.table.booked')</th>
                <th>@lang('order_payments.table.left')</th>
                <th>@lang('order_payments.table.promise')</th>
            </tr>
            </thead>
            <tbody>
            @php
                $sumOfOrders = 0;
                $sumOfItems = 0;
            @endphp
            @foreach($order->customer->orders()->where('status_id', '!=', '8')->where('status_id', '!=', '6')->get() as $itemOrder)
                <tr>
                    <td>{{ $itemOrder->id }}</td>
                    <td>{{ $itemOrder->status->name }}</td>
                    @php
                        $sumOfItems = 0;
                        foreach ($itemOrder->items as $item) {
                            $sumOfItems += ($item->net_selling_price_commercial_unit * $item->quantity * 1.23);
                        }
                        $orderValue = str_replace(',', '', number_format($sumOfItems + $itemOrder->shipment_price_for_client + $itemOrder->additional_service_cost + $itemOrder->additional_cash_on_delivery_cost, 2));
                    @endphp
                    @php
                        $sumOfOrders = $sumOfOrders + $orderValue
                    @endphp
                    <td>{{ $orderValue }}</td>
                    @php
                        $paymentsValue = 0;
                    @endphp
                    @foreach($itemOrder->bookedPayments($itemOrder->id) as $payment)
                        @php
                            $paymentsValue += $payment->amount
                        @endphp
                    @endforeach
                    <td>{{ $paymentsValue }}</td>
                    <td>{{ $orderValue - $paymentsValue }}</td>
                    <td>
                        @foreach($itemOrder->promisePayments($itemOrder->id) as $payment)
                            {{ $payment->amount }}
                        @endforeach
                    </td>
                </tr>
            @endforeach
            <tr>
                <td><h2>Suma faktur: <b style="color: red;">{{ $sumOfOrders }} zł</b></h2></td>
            </tr>

            </tbody>
        </table>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Przydziel wpłatę do zamówienia</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ action('OrdersPaymentsController@store') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="firms-general" id="orderPayment">
                            <div class="form-group">
                                <label for="amount">@lang('order_payments.form.amount')</label>
                                @foreach($order->customer->orders as $itemCustomerOrder)
                                    @php
                                        $sumOfItems = 0;
                                        foreach ($itemCustomerOrder->items as $item) {
                                            $sumOfItems += ($item->net_selling_price_commercial_unit * $item->quantity * 1.23);
                                        }
                                        $orderValue = str_replace(',', '', number_format($sumOfItems + $itemCustomerOrder->shipment_price_for_client + $itemCustomerOrder->additional_service_cost + $itemCustomerOrder->additional_cash_on_delivery_cost, 2));
                                    @endphp
                                    @break
                                @endforeach
                                <input type="text" class="form-control" id="amount" name="amount"
                                       value="{{ $orderValue }}">
                            </div>
                            <div class="form-group">
                                <label for="chooseOrder">Wybierz zlecenie</label>
                                <select class="form-control" id="chooseOrder" name="chooseOrder">
                                    @foreach($order->customer->orders as $itemCustomerOrder)
                                        @php
                                            $sumOfItems = 0;
                                            foreach ($itemCustomerOrder->items as $item) {
                                                $sumOfItems += ($item->net_selling_price_commercial_unit * $item->quantity * 1.23);
                                            }
                                            $orderValue = str_replace(',', '', number_format($sumOfItems + $itemCustomerOrder->shipment_price_for_client + $itemCustomerOrder->additional_service_cost + $itemCustomerOrder->additional_cash_on_delivery_cost, 2));
                                        @endphp
                                        <option value="{{ $itemCustomerOrder->id }}">Zlecenie: {{ $itemCustomerOrder->id }} Kwota zlecenia: {{ $orderValue }}</option>
                                    @endforeach
                                </select>
                                @foreach($order->customer->orders as $itemCustomerOrder)
                                    @php
                                        $sumOfItems = 0;
                                        foreach ($itemCustomerOrder->items as $item) {
                                            $sumOfItems += ($item->net_selling_price_commercial_unit * $item->quantity * 1.23);
                                        }
                                        $orderValue = str_replace(',', '', number_format($sumOfItems + $itemCustomerOrder->shipment_price_for_client + $itemCustomerOrder->additional_service_cost + $itemCustomerOrder->additional_cash_on_delivery_cost, 2));
                                    @endphp
                                    <input type="hidden" name="order-payment-{{$itemCustomerOrder->id}}" value="{{ $orderValue }}">
                                @endforeach
                            </div>
                            <input type="hidden" value="0" name="masterPaymentId">
                            <input type="hidden" value="0" name="masterPaymentAmount">
                        </div>
                        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="promiseModal" tabindex="-1" role="dialog" aria-labelledby="promiseModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Zaksięguj wpłatę</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ action('OrdersPaymentsController@bookPayment') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="firms-general" id="orderPayment">
                            <div class="form-group">
                                <label for="amount">@lang('order_payments.form.amount')</label>
                                <input type="text" class="form-control" id="amount" name="amount">
                            </div>
                            <input type="hidden" value="0" name="masterPaymentId">
                            <input type="hidden" value="{{ $order->id }}" name="promiseOrderId">
                        </div>
                        <button type="submit" class="btn btn-primary">Zaksięguj</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="order-tasks" id="order-tasks">
        <table style="width: 100%" id="dataTableOrderTasks" class="table table-hover">
            <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>@lang('order_tasks.table.description')</th>
                <th>@lang('order_tasks.table.title')</th>
                <th>@lang('order_tasks.table.show_label_at')</th>
                <th>@lang('order_tasks.table.status')</th>
                <th>@lang('order_tasks.table.created_at')</th>
                <th>@lang('voyager.generic.actions')</th>
            </tr>
            </thead>
        </table>
    </div>
    <div class="order-packages" id="order-packages">
        <table style="width: 100%" id="dataTableOrderPackages" class="table table-hover">
            <thead>
            <tr>
                <th></th>
                <th>@lang('voyager.generic.actions')</th>
                <th>ID</th>
                <th>@lang('order_packages.table.number')</th>
                <th>@lang('order_packages.table.status')</th>
                <th>@lang('order_packages.table.letter_number')</th>
                <th>@lang('order_packages.table.size_a')</th>
                <th>@lang('order_packages.table.size_b')</th>
                <th>@lang('order_packages.table.size_c')</th>
                <th>@lang('order_packages.table.weight')</th>
                <th>@lang('order_packages.table.container_type')</th>
                <th>@lang('order_packages.table.shape')</th>
                <th>@lang('order_packages.table.cash_on_delivery')</th>
                <th>@lang('order_packages.table.notices')</th>
                <th>@lang('order_packages.table.delivery_courier_name')</th>
                <th>@lang('order_packages.table.service_courier_name')</th>
                <th>@lang('order_packages.table.shipment_date')</th>
                <th>@lang('order_packages.table.delivery_date')</th>
                <th>@lang('order_packages.table.cost_for_client')</th>
                <th>@lang('order_packages.table.cost_for_company')</th>
                <th>@lang('order_packages.table.real_cost_for_company')</th>
                <th>@lang('order_packages.table.sending_number')</th>
                <th>@lang('order_packages.table.quantity')</th>
                <th>@lang('order_packages.table.created_at')</th>
            </tr>
            </thead>
        </table>
    </div>
    <div class="order-messages" id="order-messages">
        <div class="panel panel-bordered">
            <div class="panel-body">
                @if(!empty($emails))
                    @foreach($emails as $email)
                        <div style="display: inline-block;">
                            <img src="https://purepng.com/public/uploads/large/purepng.com-mail-iconsymbolsiconsapple-iosiosios-8-iconsios-8-721522596075clftr.png" alt="" style="width: 50px; height: 50px;">
                            <div style="
    display: inline-block;
">
                                <span>{{ str_replace('+0100', '', $email->timestamp) }}</span>

                                <a href="{{ Storage::url('mails/' . $email->path) }}" style="display: block;">Ściągnij</a>
                            </div>

                        </div>
                    @endforeach
                @endif
                    <hr>
                @foreach($messages as $message)
                    @switch($message->type)
                        @case('GENERAL')
                        <div class="alert alert-info">
                            @break
                            @case('SHIPPING')
                            <div class="alert alert-success" role="alert">
                                @break
                                @case('WAREHOUSE')
                                <div class="alert alert-warning" role="alert">
                                    @break
                                    @case('COMPLAINT')
                                    <div class="alert alert-danger" role="alert">
                                        @break
                                        @endswitch
                                        <h3>
                                            @if($message->source == "MAIL")
                                                [MAIL] -
                                            @endif
                                            @if($message->user_id == null)
                                                [KLIENT]
                                            @else
                                                [KONSULTANT]
                                            @endif
                                        </h3>
                                        <p><span class="icon voyager-categories" style="margin-right: 5px;"></span>{{ $message->type }}</p>
                                        <p><span class="icon voyager-calendar" style="margin-right: 5px;"></span> {{ $message->created_at }}</p>
                                        @if(count($message->attachments))
                                            <p><span class="icon voyager-images" style="margin-right: 5px;"></span>
                                                <style type="text/css">
                                                    span.order-message-attachment-link:not(:last-child):after {
                                                        content: " | ";
                                                    }
                                                </style>
                                                Załączniki:
                                                @foreach($message->attachments as $attachment)
                                                    <span class="order-message-attachment-link"><a style="color: white;" href="{{asset('storage/attachments/' . $attachment->message->order_id . '/' . $attachment->order_message_id . '/' . $attachment->file)}}" target="_blank">{{$attachment->file}}</a></span>
                                                @endforeach
                                            </p>
                                        @endif
                                        <h4 style="margin-top: 10px;">{{ $message->title }}</h4>
                                        <p>@if($message->additional_description) <span style="font-weight: bolder;">Opis reklamacji:</span>  @endif {{ $message->message }}</p>
                                        @if($message->additional_description)
                                            <p><span style="font-weight: bolder;">Opis roszczenia reklamacyjnego:</span> {{ $message->additional_description }}</p>
                                        @endif
                                    </div>
                                    @endforeach


                                    <form method="POST" action="{{ route('order_messages.store') }}" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label for="title">Tytuł</label>
                                            <input type="text" name="title" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="type">Typ</label>
                                            <select name="type" id="type" class="form-control">
                                                <option value="GENERAL">Ogólne</option>
                                                <option value="SHIPPING">Wysyłka</option>
                                                <option value="WAREHOUSE">Magazyn</option>
                                                <option value="COMPLAINT">Skarga</option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="employee_id" value="{{ Auth::id() }}">
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <div class="form-group">
                                            <label for="message">Wiadomość</label>
                                            <textarea name="message" id="message" class="form-control" rows="10"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="attachment">Załącznik</label>
                                            <input type="file" name="attachment" id="attachment">
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Wyślij</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document" style="width: 90%;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <button type="button" class="btn btn-success" id="newSplitOrder">
                                            Dodaj nowe zamówienie
                                        </button>
                                        <form id="splitOrders" action="{{ action('OrdersController@splitOrders')}}" method="post">
                                            {{ csrf_field() }}
                                            <div>
                                                <label for="splitAndUpdate">Wydziel produkty do nowych zamówień i zaktualizuj zlecenie główne</label>
                                                <input type="checkbox" name="splitAndUpdate">
                                            </div>

                                            <input type="hidden" value="{{ $order->id }}" name="orderId">
                                            <table id="productsTable" class="table table1 table-venice-blue" style="width: 100%;">
                                                <tbody id="products-tbody">
                                                <tr>
                                                    <td colspan="4" style="border: 0px;"></td>
                                                    <td style="border: 0px;" class="firstOrder">Zamówienie 1 <input type="hidden" name="firstOrderExist" value="0"></td>
                                                    <td style="border: 0px;" class="secondOrder">Zamówienie 2 <input type="hidden" name="secondOrderExist" value="0"></td>
                                                    <td style="border: 0px;" class="thirdOrder">Zamówienie 3 <input type="hidden" name="thirdOrderExist" value="0"></td>
                                                    <td style="border: 0px;" class="fourthOrder">Zamówienie 4 <input type="hidden" name="fourthOrderExist" value="0"></td>
                                                    <td style="border: 0px;" class="fifthOrder">Zamówienie 5 <input type="hidden" name="fifthOrderExist"  value="0"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" style="border: 0px;"></td>
                                                    <td style="border: 0px; color: red;" class="firstOrderWeightSum">Waga: 0 <input type="hidden" name="firstOrderWeightSum" value="0"></td>
                                                    <td style="border: 0px; color: red;" class="secondOrderWeightSum">Waga: 0 <input type="hidden" name="secondOrderWeightSum" value="0"></td>
                                                    <td style="border: 0px; color: red;" class="thirdOrderWeightSum">Waga: 0 <input type="hidden" name="thirdOrderWeightSum" value="0"></td>
                                                    <td style="border: 0px; color: red;" class="fourthOrderWeightSum">Waga: 0 <input type="hidden" name="fourthOrderWeightSum" value="0"></td>
                                                    <td style="border: 0px; color: red;" class="fifthOrderWeightSum">Waga: 0 <input type="hidden" name="fifthOrderWeightSum" value="0"></td>
                                                </tr>
                                                @foreach($order->items as $item)
                                                    @php
                                                        $gross_purchase_sum += ($item->net_purchase_price_commercial_unit * $item->quantity * 1.23);
                                                        $net_purchase_sum += $item->net_purchase_price_commercial_unit * $item->quantity ;
                                                        $gross_selling_sum += ($item->net_selling_price_commercial_unit * $item->quantity * 1.23);
                                                        $net_selling_sum += $item->net_selling_price_commercial_unit * $item->quantity;
                                                        $weight += $item->product->weight_trade_unit * $item->quantity;
                                                    @endphp
                                                    <tr class="id row-{{$item->id}}" id="id[{{$item->id}}]">
                                                        <td colspan="4"><h4><img src="{!! str_replace('C:\\z\\', 'https://mega1000.pl/', $item->product->url) !!}" style="width: 179px; height: 130px;"><strong>{{ $loop->iteration }}. </strong>{{ $item->product->name }} (symbol: {{ $item->product->symbol }}) </h4></td>


                                                        <input name="id[{{$item->id}}]"
                                                               value="{{ $item->id }}" type="hidden"
                                                               class="form-control" id="id[{{$item->id}}]">
                                                        <input name="product_id[{{$item->id}}]"
                                                               value="{{ $item->product_id }}" type="hidden"
                                                               class="form-control" id="product_id[{{$item->id}}]">

                                                        <input
                                                                value="{{ $item->quantity }}" type="hidden"
                                                                class="form-control item_quantity"  name="item_quantity[{{$item->id}}]" data-item-id="{{$item->id}}">

                                                        <input name="numbers_of_basic_commercial_units_in_pack[{{$item->id}}]"
                                                               data-item-id="{{$item->id}}" value="{{ $item->product->packing->numbers_of_basic_commercial_units_in_pack }}" type="hidden"
                                                               class="form-control numbers_of_basic_commercial_units_in_pack" id="numbers_of_basic_commercial_units_in_pack[{{$item->id}}]">
                                                        <input name="number_of_sale_units_in_the_pack[{{$item->id}}]"
                                                               data-item-id="{{$item->id}}" value="{{ $item->product->packing->number_of_sale_units_in_the_pack }}" type="hidden"
                                                               class="form-control number_of_sale_units_in_the_pack" id="number_of_sale_units_in_the_pack[{{$item->id}}]">
                                                        <input name="number_of_trade_items_in_the_largest_unit[{{$item->id}}]"
                                                               data-item-id="{{$item->id}}" value="{{ $item->product->packing->number_of_trade_items_in_the_largest_unit }}" type="hidden"
                                                               class="form-control number_of_trade_items_in_the_largest_unit" id="number_of_trade_items_in_the_largest_unit[{{$item->id}}]">
                                                        <input name="unit_consumption[{{$item->id}}]"
                                                               data-item-id="{{$item->id}}" value="{{ $item->product->packing->unit_consumption }}" type="hidden"
                                                               class="form-control unit_consumption" id="unit_consumption[{{$item->id}}]">
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4">
                                                            Obliczenia dokonano przy założeniu  {{$item->product->packing->unit_consumption}}  {{$item->product->packing->unit_basic}} / {{$item->product->packing->calculation_unit}}
                                                            <br> 1 {{$item->product->packing->unit_of_collective}} = {{$item->product->packing->number_of_sale_units_in_the_pack}} ({{$item->product->packing->unit_commercial}}) = @if(is_numeric($item->product->packing->number_of_sale_units_in_the_pack) && is_numeric($item->product->packing->unit_consumption)){{$item->product->packing->number_of_sale_units_in_the_pack / $item->product->packing->unit_consumption}} @else {{0}} @endif ({{$item->product->packing->calculation_unit}} / {{$item->product->packing->unit_of_collective}})
                                                        </td>
                                                    </tr>
                                                    <tr class="row-{{$item->id}}">
                                                        <th colspan="4">Cena zakupu</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Jednostka handlowa ({{ $item->product->packing->unit_commercial }})</td>
                                                        <td>Jednostka podstawowa ({{$item->product->packing->unit_basic}})</td>
                                                        <td>Jednostka obliczeniowa ({{$item->product->packing->calculation_unit}})</td>
                                                        <td>Jednostka zbirocza ({{$item->product->packing->unit_of_collective}})</td>
                                                    </tr>
                                                    <tr class="purchase-row row-{{$item->id}}">
                                                        <td>
                                                            <input name="net_purchase_price_commercial_unit[{{$item->id}}]"
                                                                   data-item-id="{{$item->id}}" value="{{ $item->net_purchase_price_commercial_unit }}" type="text"
                                                                   class="form-control price net_purchase_price_commercial_unit priceChange" id="net_purchase_price_commercial_unit[{{$item->id}}]">
                                                        </td>
                                                        <td>
                                                            <input name="net_purchase_price_basic_unit[{{$item->id}}]"
                                                                   data-item-id="{{$item->id}}" value="{{ $item->net_purchase_price_basic_unit }}" type="text"
                                                                   class="form-control price net_purchase_price_basic_unit priceChange" id="net_purchase_price_basic_unit[{{$item->id}}]">
                                                        </td>
                                                        <td>
                                                            <input name="net_purchase_price_calculated_unit[{{$item->id}}]"
                                                                   data-item-id="{{$item->id}}" value="{{ $item->net_purchase_price_calculated_unit }}" type="text"
                                                                   class="form-control price net_purchase_price_calculated_unit priceChange" id="net_purchase_price_calculated_unit[{{$item->id}}]">
                                                        </td>
                                                        <td>
                                                            <input name="net_purchase_price_aggregate_unit[{{$item->id}}]"
                                                                   data-item-id="{{$item->id}}" value="{{ $item->net_purchase_price_aggregate_unit }}" type="text"
                                                                   class="form-control price net_purchase_price_aggregate_unit priceChange" id="net_purchase_price_aggregate_unit[{{$item->id}}]">
                                                        </td>
                                                    </tr>
                                                    <tr class="purchase-row row-{{$item->id}}">
                                                        <td>
                                                            <input name="gross_purchase_price_commercial_unit[{{$item->id}}]"
                                                                   value="" type="text"
                                                                   data-item-id="{{$item->id}}" class="form-control price gross_purchase_price_commercial_unit priceChange" id="gross_purchase_price_commercial_unit[{{$item->id}}]" >
                                                        </td>
                                                        <td>
                                                            <input name="gross_purchase_price_basic_unit[{{$item->id}}]"
                                                                   value="" type="text"
                                                                   data-item-id="{{$item->id}}" class="form-control price gross_purchase_price_basic_unit priceChange" id="gross_purchase_price_basic_unit[{{$item->id}}]" >
                                                        </td>
                                                        <td>
                                                            <input name="gross_purchase_price_calculated_unit[{{$item->id}}]"
                                                                   value="" type="text"
                                                                   data-item-id="{{$item->id}}" class="form-control price gross_purchase_price_calculated_unit priceChange" id="gross_purchase_price_calculated_unit[{{$item->id}}]" >
                                                        </td>
                                                        <td>
                                                            <input name="gross_purchase_price_aggregate_unit[{{$item->id}}]"
                                                                   value="" type="text"
                                                                   data-item-id="{{$item->id}}" class="form-control price gross_purchase_price_aggregate_unit priceChange" id="gross_purchase_price_aggregate_unit[{{$item->id}}]" >
                                                        </td>
                                                    </tr>
                                                    <tr class="row-{{$item->id}}">
                                                        <th colspan="4">Cena sprzedaży</th>
                                                    </tr>
                                                    <tr class="selling-row row-{{$item->id}}">
                                                        <td>
                                                            <input name="net_selling_price_commercial_unit[{{$item->id}}]"
                                                                   data-item-id="{{$item->id}}" value="{{ $item->net_selling_price_commercial_unit }}" type="text"
                                                                   class="form-control price net_selling_price_commercial_unit priceChange change-order" id="net_selling_price_commercial_unit[{{$item->id}}]">
                                                        </td>
                                                        <td>
                                                            <input name="net_selling_price_basic_unit[{{$item->id}}]"
                                                                   data-item-id="{{$item->id}}" value="{{ $item->product->net_selling_price_basic_unit }}" type="text"
                                                                   class="form-control price net_selling_price_basic_unit priceChange change-order" id="net_selling_price_basic_unit[{{$item->id}}]">
                                                        </td>
                                                        <td>
                                                            <input name="net_selling_price_calculated_unit[{{$item->id}}]"
                                                                   data-item-id="{{$item->id}}" value="{{ $item->product->net_selling_price_calculated_unit }}" type="text"
                                                                   class="form-control price net_selling_price_calculated_unit priceChange change-order" id="net_selling_price_calculated_unit[{{$item->id}}]">
                                                        </td>
                                                        <td>
                                                            <input name="net_selling_price_aggregate_unit[{{$item->id}}]"
                                                                   data-item-id="{{$item->id}}" value="{{ $item->product->  net_selling_price_aggregate_unit }}" type="text"
                                                                   class="form-control price net_selling_price_aggregate_unit priceChange change-order" id="net_selling_price_aggregate_unit[{{$item->id}}]">
                                                        </td>
                                                    </tr>
                                                    <tr class="selling-row row-{{$item->id}}">
                                                        <td>
                                                            <input name="gross_selling_price_commercial_unit[{{$item->id}}]"
                                                                   data-item-id="{{$item->id}}" value="" type="text"
                                                                   class="form-control price gross_selling_price_commercial_unit priceChange change-order" id="gross_selling_price_commercial_unit[{{$item->id}}]" >
                                                        </td>
                                                        <td>
                                                            <input name="gross_selling_price_basic_unit[{{$item->id}}]"
                                                                   data-item-id="{{$item->id}}" value="" type="text"
                                                                   class="form-control price gross_selling_price_basic_unit priceChange change-order" id="gross_selling_price_basic_unit[{{$item->id}}]" >
                                                        </td>
                                                        <td>
                                                            <input name="gross_selling_price_calculated_unit[{{$item->id}}]"
                                                                   data-item-id="{{$item->id}}" value="" type="text"
                                                                   class="form-control price gross_selling_price_calculated_unit priceChange change-order" id="gross_selling_price_calculated_unit[{{$item->id}}]" >
                                                        </td>
                                                        <td>
                                                            <input name="gross_selling_price_aggregate_unit[{{$item->id}}]"
                                                                   data-item-id="{{$item->id}}" value="" type="text"
                                                                   class="form-control price gross_selling_price_aggregate_unit priceChange change-order" id="gross_selling_price_aggregate_unit[{{$item->id}}]" >
                                                        </td>
                                                    </tr>
                                                    <tr class="selling-row row-{{$item->id}}">
                                                        @foreach($productPacking as $packing)
                                                            @if($packing->product_id === $item->product_id)
                                                                <td>
                                                                    <input name="unit_commercial"
                                                                           value="{{$item->quantity . ' ' . $packing->unit_commercial }}" type="text"
                                                                           class="form-control" id="unit_commercial" disabled>
                                                                </td>
                                                                <td>
                                                                    <input name="unit_basic"
                                                                           value="@if($item->product->packing->numbers_of_basic_commercial_units_in_pack != 0){{$item->quantity / $item->product->packing->numbers_of_basic_commercial_units_in_pack  .' '.$packing->unit_basic }} @else {{0}} @endif" type="text"
                                                                           class="form-control" id="unit_basic" disabled>
                                                                </td>
                                                                <td>
                                                                    <input name="calculation_unit[{{$item->id}}]"
                                                                           value="@if(is_numeric($item->product->packing->numbers_of_basic_commercial_units_in_pack) && is_numeric($item->product->packing->unit_consumption)){{ number_format($item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack / $item->product->packing->unit_consumption, 2) .' '.$packing->calculation_unit }} @else {{0}} @endif" type="text"
                                                                           class="form-control" id="calculation_unit" disabled>
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        if (empty($item->product->packing->number_of_sale_units_in_the_pack))
                                                                            $a = 0;
                                                                        else
                                                                            $a = $item->quantity * $item->product->packing->number_of_sale_units_in_the_pack;
                                                                    @endphp
                                                                    <input name="unit_of_collective"
                                                                           value="{{ number_format($a, 4) .' '.$packing->unit_of_collective}} " type="text"
                                                                           class="form-control" id="unit_of_collective" disabled>
                                                                </td>
                                                            @endif
                                                        @endforeach
                                                    </tr>
                                                    <tr class="row-{{$item->id}}">
                                                        <th colspan="4">Ilość</th>
                                                    </tr>
                                                    <tr class="selling-row row-{{$item->id}}">
                                                        <td>
                                                            <input name="modal_quantity_commercial[{{$item->id}}]"
                                                                   value="{{ $item->quantity }}" type="text" data-item-id="{{$item->id}}"
                                                                   class="form-control" id="modal_quantity_commercial[{{$item->id}}]">
                                                            <input type="hidden" name="modal_weight[{{$item->id}}]" data-item-id="{{$item->id}}" value="{{ $item->product->weight_trade_unit ?? 0 }}">
                                                        </td>
                                                        <td colspan="3"></td>
                                                        <td class="firstOrder"><input name="firstOrderQuantity[{{$item->id}}]"
                                                                                      type="text" data-item-id="{{$item->id}}"
                                                                                      data-order-type="first"
                                                                                      class="form-control splitQuantity" id="firstOrderQuantity[{{$item->id}}]">
                                                            <p>Ilość startowa: <span name="base[{{$item->id}}]" data-order-type="first">{{ $item->quantity }}</span> <button type="button" onclick="fillQuantity({{$item->id}},{{ $item->quantity }}, 'first' )"><</button></p></p>
                                                            <p>Zostało: <span name="left[{{$item->id}}]">{{ $item->quantity }}</span></p>
                                                            <p>Waga: <span name="firstOrderWeight[{{$item->id}}]"></span>
                                                                <input type="hidden" class="firstWeightValue" name="firstOrderWeightValue[{{$item->id}}]" value="{{ $item->product->weight_trade_unit ?? 0 }}">
                                                                <input type="hidden" class="firstWeightValueBase" name="firstOrderWeightValueBase[{{$item->id}}]" value="0">
                                                            </p>
                                                        </td>
                                                        <td class="secondOrder"><input name="secondOrderQuantity[{{$item->id}}]"
                                                                                       type="text" data-item-id="{{$item->id}}"
                                                                                       data-order-type="second"
                                                                                       class="form-control splitQuantity" id="secondOrderQuantity[{{$item->id}}]">
                                                            <p>Ilość startowa: <span name="base[{{$item->id}}]" data-order-type="second">{{ $item->quantity }}</span> <button type="button" onclick="fillQuantity({{$item->id}},{{ $item->quantity }}, 'second' )"><</button></p></p>
                                                            <p>Zostało: <span name="left[{{$item->id}}]">{{ $item->quantity }}</span></p>
                                                            <p>Waga: <span name="secondOrderWeight[{{$item->id}}]"></span>
                                                                <input type="hidden" class="secondWeightValue" name="secondOrderWeightValue[{{$item->id}}]" value="{{ $item->product->weight_trade_unit ?? 0 }}">
                                                                <input type="hidden" class="secondWeightValueBase" name="secondOrderWeightValueBase[{{$item->id}}]" value="0">
                                                            </p>
                                                        </td>
                                                        <td class="thirdOrder"><input name="thirdOrderQuantity[{{$item->id}}]"
                                                                                      type="text" data-item-id="{{$item->id}}"
                                                                                      data-order-type="third"
                                                                                      class="form-control splitQuantity" id="thirdOrderQuantity[{{$item->id}}]">
                                                            <p>Ilość startowa: <span name="base[{{$item->id}}]" data-order-type="third">{{ $item->quantity }}</span> <button type="button" onclick="fillQuantity({{$item->id}},{{ $item->quantity }}, 'third' )"><</button></p></p>
                                                            <p>Zostało: <span name="left[{{$item->id}}]">{{ $item->quantity }}</span></p>
                                                            <p>Waga: <span name="thirdOrderWeight[{{$item->id}}]"></span>
                                                                <input type="hidden" class="thirdWeightValue" name="thirdOrderWeightValue[{{$item->id}}]" value="{{ $item->product->weight_trade_unit ?? 0 }}">
                                                                <input type="hidden" class="thirdWeightValueBase" name="thirdOrderWeightValueBase[{{$item->id}}]" value="0">
                                                            </p>
                                                        </td>
                                                        <td class="fourthOrder"><input name="fourthOrderQuantity[{{$item->id}}]"
                                                                                       type="text" data-item-id="{{$item->id}}"
                                                                                       data-order-type="fourth"
                                                                                       class="form-control splitQuantity" id="fourthOrderQuantity[{{$item->id}}]">
                                                            <p>Ilość startowa: <span name="base[{{$item->id}}]" data-order-type="fourth">{{ $item->quantity }}</span> <button type="button" onclick="fillQuantity({{$item->id}},{{ $item->quantity }}, 'fourth' )"><</button></p>
                                                            <p>Zostało: <span name="left[{{$item->id}}]">{{ $item->quantity }}</span></p>
                                                            <p>Waga: <span name="fourthOrderWeight[{{$item->id}}]"></span>
                                                                <input type="hidden" class="fourthWeightValue" name="fourthOrderWeightValue[{{$item->id}}]" value="{{ $item->product->weight_trade_unit ?? 0 }}">
                                                                <input type="hidden" class="fourthWeightValueBase" name="fourthOrderWeightValueBase[{{$item->id}}]" value="0">
                                                            </p>
                                                        </td>
                                                        <td class="fifthOrder"><input name="fifthOrderQuantity[{{$item->id}}]"
                                                                                      type="text" data-item-id="{{$item->id}}"
                                                                                      data-order-type="fifth"
                                                                                      class="form-control splitQuantity" id="fifthOrderQuantity[{{$item->id}}]">
                                                            <p>Ilość startowa: <span name="base[{{$item->id}}]" data-order-type="fifth">{{ $item->quantity }}</span> <button type="button" onclick="fillQuantity({{$item->id}},{{ $item->quantity }}, 'fifth' )"><</button></p>
                                                            <p>Zostało: <span name="left[{{$item->id}}]">{{ $item->quantity }}</span></p>
                                                            <p>Waga: <span name="fifthOrderWeight[{{$item->id}}]"></span>
                                                                <input type="hidden" class="fifthWeightValue" name="fifthOrderWeightValue[{{$item->id}}]" value="{{ $item->product->weight_trade_unit ?? 0 }}">
                                                                <input type="hidden" class="fifthWeightValueBase" name="fifthrderWeightValueBase[{{$item->id}}]" value="0">
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr class="row-{{$item->id}}">
                                                        <th colspan="4">Zysk</th>
                                                    </tr>
                                                    <tr class="selling-row row-{{$item->id}}">
                                                        <td>
                                                            <input type="text" class="form-control item-profit priceChange" data-item-id="{{$item->id}}" disabled name="item-profit"
                                                                   value="{{ number_format(($item->net_selling_price_commercial_unit * $item->quantity * 1.23) - ($item->net_purchase_price_commercial_unit * $item->quantity * 1.23), 2) }}">
                                                        </td>
                                                        <td colspan="3"></td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="4">
                                                        <button class="btn btn-info btn-split splitDko" type="button" style="display: none; margin-top: -10px; margin-bottom: 55px; margin-left: auto;">Rozdziel po równo DKO</button>
                                                        <button class="btn btn-info btn-split splitDkp" type="button" style="display: none; margin-bottom: 55px; margin-left: auto;">Rozdziel po równo DKP</button>
                                                        <button class="btn btn-info btn-split splitClient" type="button" style="display: none; margin-bottom: 55px; margin-left: auto;">Rozdziel po równo koszt klienta</button>
                                                        <button class="btn btn-info btn-split splitUs" type="button" style="display: none; margin-left: auto;">Rozdziel po równo nasz koszt</button>
                                                    </td>
                                                    <td class="firstOrder">
                                                        <div class="form-group">
                                                            <label for="additional_service_cost_firstOrder">Dodaktowy koszt obsługi</label>
                                                            <input type="text" class="form-control dkoInput" id="additional_service_cost_firstOrder" name="additional_service_cost_firstOrder">
                                                            <p>Zostało: <span class="dkoLeft">{{ $order->additional_service_cost }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="packing_warehouse_cost_firstOrder">Dodatkowy koszt pobrania</label>
                                                            <input class="form-control dkpInput" id="additional_cash_on_delivery_cost_firstOrder" name="additional_cash_on_delivery_cost_firstOrder">
                                                            <p>Zostało: <span class="dkpLeft">{{ $order->additional_cash_on_delivery_cost }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="shipment_price_for_client_firstOrder">Koszt transportu dla klienta brutto</label>
                                                            <input type="text" class="form-control shipmentClient" id="shipment_price_for_client_firstOrder" name="shipment_price_for_client_firstOrder">
                                                            <p>Zostało: <span class="shipmentClientLeft">{{ $order->shipment_price_for_client }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="shipment_price_for_us_firstOrder">Koszt transportu dla firmy brutto</label>
                                                            <input type="text" class="form-control shipmentUs" id="shipment_price_for_us_firstOrder" name="shipment_price_for_us_firstOrder">
                                                            <p>Zostało: <span class="shipmentUsLeft">{{ $order->shipment_price_for_us }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="proposed_payment_firstOrder">Proponowana zaliczka</label>
                                                            <input type="text" class="form-control" id="proposed_payment_firstOrder" name="proposed_payment_firstOrder">
                                                        </div>
                                                    </td>
                                                    <td class="secondOrder">
                                                        <div class="form-group">
                                                            <label for="additional_service_cost_secondOrder">Dodaktowy koszt obsługi</label>
                                                            <input type="text" class="form-control dkoInput" id="additional_service_cost_secondOrder" name="additional_service_cost_secondOrder">
                                                            <p>Zostało: <span class="dkoLeft">{{ $order->additional_service_cost }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="packing_warehouse_cost_secondOrder">Dodatkowy koszt pobrania</label>
                                                            <input class="form-control dkpInput" id="additional_cash_on_delivery_cost_secondOrder" name="additional_cash_on_delivery_cost_secondOrder">
                                                            <p>Zostało: <span class="dkpLeft">{{ $order->additional_cash_on_delivery_cost }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="shipment_price_for_client_secondOrder">Koszt transportu dla klienta brutto</label>
                                                            <input type="text" class="form-control shipmentClient" id="shipment_price_for_client_secondOrder" name="shipment_price_for_client_secondOrder">
                                                            <p>Zostało: <span class="shipmentClientLeft">{{ $order->shipment_price_for_client }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="shipment_price_for_us_secondOrder">Koszt transportu dla firmy brutto</label>
                                                            <input type="text" class="form-control shipmentUs" id="shipment_price_for_us_secondOrder" name="shipment_price_for_us_secondOrder">
                                                            <p>Zostało: <span class="shipmentUsLeft">{{ $order->shipment_price_for_us }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="proposed_payment_secondOrder">Proponowana zaliczka</label>
                                                            <input type="text" class="form-control" id="proposed_payment_secondOrder" name="proposed_payment_secondOrder">
                                                        </div>
                                                    </td>
                                                    <td class="thirdOrder">
                                                        <div class="form-group">
                                                            <label for="additional_service_cost_thirdOrder">Dodaktowy koszt obsługi</label>
                                                            <input type="text" class="form-control dkoInput" id="additional_service_cost_thirdOrder" name="additional_service_cost_thirdOrder">
                                                            <p>Zostało: <span class="dkoLeft">{{ $order->additional_service_cost }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="packing_warehouse_cost_thirdOrder">Dodatkowy koszt pobrania</label>
                                                            <input class="form-control dkpInput" id="additional_cash_on_delivery_cost_thirdOrder" name="additional_cash_on_delivery_cost_thirdOrder">
                                                            <p>Zostało: <span class="dkpLeft">{{ $order->additional_cash_on_delivery_cost }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="shipment_price_for_client_thirdOrder">Koszt transportu dla klienta brutto</label>
                                                            <input type="text" class="form-control shipmentClient" id="shipment_price_for_client_thirdOrder" name="shipment_price_for_client_thirdOrder">
                                                            <p>Zostało: <span class="shipmentClientLeft">{{ $order->shipment_price_for_client }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="shipment_price_for_us_thirdOrder">Koszt transportu dla firmy brutto</label>
                                                            <input type="text" class="form-control shipmentUs" id="shipment_price_for_us_thirdOrder" name="shipment_price_for_us_thirdOrder">
                                                            <p>Zostało: <span class="shipmentUsLeft">{{ $order->shipment_price_for_us }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="proposed_payment_thirdOrder">Proponowana zaliczka</label>
                                                            <input type="text" class="form-control" id="proposed_payment_thirdOrder" name="proposed_payment_thirdOrder">
                                                        </div>
                                                    </td>
                                                    <td class="fourthOrder">
                                                        <div class="form-group">
                                                            <label for="additional_service_cost_fourthOrder">Dodaktowy koszt obsługi</label>
                                                            <input type="text" class="form-control dkoInput" id="additional_service_cost_fourthOrder" name="additional_service_cost_fourthOrder">
                                                            <p>Zostało: <span class="dkoLeft">{{ $order->additional_service_cost }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="packing_warehouse_cost_fourthOrder">Dodatkowy koszt pobrania</label>
                                                            <input class="form-control dkpInput" id="additional_cash_on_delivery_cost_fourthOrder" name="additional_cash_on_delivery_cost_fourthOrder">
                                                            <p>Zostało: <span class="dkpLeft">{{ $order->additional_cash_on_delivery_cost }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="shipment_price_for_client_fourthOrder">Koszt transportu dla klienta brutto</label>
                                                            <input type="text" class="form-control shipmentClient" id="shipment_price_for_client_fourthOrder" name="shipment_price_for_client_fourthOrder">
                                                            <p>Zostało: <span class="shipmentClientLeft">{{ $order->shipment_price_for_client }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="shipment_price_for_us_fourthOrder">Koszt transportu dla firmy brutto</label>
                                                            <input type="text" class="form-control shipmentUs" id="shipment_price_for_us_fourthOrder" name="shipment_price_for_us_fourthOrder">
                                                            <p>Zostało: <span class="shipmentUsLeft">{{ $order->shipment_price_for_us }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="proposed_payment_fourthOrder">Proponowana zaliczka</label>
                                                            <input type="text" class="form-control" id="proposed_payment_fourthOrder" name="proposed_payment_fourthOrder">
                                                        </div>
                                                    </td>
                                                    <td class="fifthOrder">
                                                        <div class="form-group">
                                                            <label for="additional_service_cost_fifthOrder">Dodaktowy koszt obsługi</label>
                                                            <input type="text" class="form-control dkoInput" id="additional_service_cost_fifthOrder" name="additional_service_cost_fifthOrder">
                                                            <p>Zostało: <span class="dkoLeft">{{ $order->additional_service_cost }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="packing_warehouse_cost_fifthOrder">Dodatkowy koszt pobrania</label>
                                                            <input class="form-control dkpInput" id="additional_cash_on_delivery_cost_fifthOrder" name="additional_cash_on_delivery_cost_fifthOrder">
                                                            <p>Zostało: <span class="dkpLeft">{{ $order->additional_cash_on_delivery_cost }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="shipment_price_for_client_fifthOrder">Koszt transportu dla klienta brutto</label>
                                                            <input type="text" class="form-control shipmentClient" id="shipment_price_for_client_fifthOrder" name="shipment_price_for_client_fifthOrder">
                                                            <p>Zostało: <span class="shipmentClientLeft">{{ $order->shipment_price_for_client }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="shipment_price_for_us_fifthOrder">Koszt transportu dla firmy brutto</label>
                                                            <input type="text" class="form-control shipmentUs" id="shipment_price_for_us_fifthOrder" name="shipment_price_for_us_fifthOrder">
                                                            <p>Zostało: <span class="shipmentUsLeft">{{ $order->shipment_price_for_us }}</span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="proposed_payment_fifthOrder">Proponowana zaliczka</label>
                                                            <input type="text" class="form-control" id="proposed_payment_fifthOrder" name="proposed_payment_fifthOrder">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6"></td><td><input type="submit" class="btn btn-primary pull-right" value="Rozdziel"></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <style>
                            .firstOrder, .secondOrder, .thirdOrder, .fourthOrder, .fifthOrder {
                                display: none;
                            }
                        </style>
                        @endsection
                @section('datatable-scripts')
                    <script type="application/javascript">
                        $(document).ready(function () {
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
                            $(function()
                            {
                                var available = [
                                    @php
                                        foreach($firms as $item){
                                             echo '"'.$item->symbol.'",';
                                             }
                                    @endphp
                                ];
                                $( "#firms_data" ).autocomplete({
                                    source: available
                                });
                            });

                            var general = $('#general').show();
                            var payments = $('#order-payments').hide();
                            var tasks = $('#order-tasks').hide();
                            var packages = $('#order-packages').hide();
                            var messages = $('#order-messages').hide();
                            var status = $('#order-status').hide();
                            var pageTitle = $('.page-title').children('i');
                            var createButtonOrderPayments = $('#create-button-orderPayments').hide();
                            var createButtonOrderTasks = $('#create-button-orderTasks').hide();
                            var createButtonOrderPackages = $('#create-button-orderPackages').hide();
                            var uri = $('#uri').val()
                            var value;
                            var referrer = document.referrer;
                            var breadcrumb = $('.breadcrumb');
                            var item = '{{old('tab')}}';
                            var addOrder = $('#new-order');
                            addOrder.hide();
                            breadcrumb.children().remove();
                            breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
                            breadcrumb.append("<li class='active'><a href='/admin/orders/{{$order->id}}/edit'>Zamówienia</a></li>");
                            breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
                            if (referrer.search('orderPayments') != -1 || uri.search('orderPayments') != -1 || item === 'orderPayments') {
                                $('#button-general').removeClass('active');
                                $('#button-tasks').removeClass('active');
                                $('#button-messages').removeClass('active');
                                $('#button-payments').addClass('active');
                                $('#button-packages').removeClass('active');
                                $('#submit').hide();
                                $('#submitOrder').hide();
                                general.hide();
                                tasks.hide();
                                payments.show();
                                packages.hide();
                                messages.hide();
                                status.hide();
                                createButtonOrderPayments.show();
                                createButtonOrderPackages.hide();
                                createButtonOrderTasks.hide();
                                pageTitle.removeClass();
                                pageTitle.addClass('voyager-wallet');
                                breadcrumb.children().last().remove();
                                breadcrumb.append("<li class='active'><a href='/admin/orders/{{$order->id}}/edit#order-payments'>Płatności</a></li>");
                                addOrder.hide();

                            } else if (referrer.search('orderTasks') != -1 || uri.search('orderTasks') != -1 || item === 'orderTasks') {
                                $('#button-general').removeClass('active');
                                $('#button-tasks').addClass('active');
                                $('#button-messages').removeClass('active');
                                $('#button-payments').removeClass('active');
                                $('#button-packages').removeClass('active');
                                $('#submit').hide();
                                $('#submitOrder').hide();
                                general.hide();
                                tasks.show();
                                payments.hide();
                                packages.hide();
                                messages.hide();
                                status.hide();
                                createButtonOrderPayments.hide();
                                createButtonOrderPackages.hide();
                                createButtonOrderTasks.show();
                                pageTitle.removeClass();
                                pageTitle.addClass('voyager-calendar');

                                breadcrumb.children().last().remove();
                                breadcrumb.append("<li class='active'><a href='/admin/orders/{{$order->id}}/edit#employees'>Zadania</a></li>");
                                addOrder.hide();

                            } else if (referrer.search('orderPackages') != -1 || uri.search('orderPackages') != -1 || item === 'orderPackages') {
                                $('#button-general').removeClass('active');
                                $('#button-tasks').removeClass('active');
                                $('#button-messages').removeClass('active');
                                $('#button-payments').removeClass('active');
                                $('#button-packages').addClass('active');
                                $('#submit').hide();
                                $('#submitOrder').hide();
                                general.hide();
                                tasks.hide();
                                payments.hide();
                                packages.show();
                                messages.hide();
                                createButtonOrderPayments.hide();
                                createButtonOrderPackages.show();
                                createButtonOrderTasks.hide();
                                pageTitle.removeClass();
                                pageTitle.addClass('voyager-archive');

                                breadcrumb.children().last().remove();
                                breadcrumb.append("<li class='active'><a href='/admin/orders/{{$order->id}}/edit'>Paczki</a></li>");
                                addOrder.hide();
                            } else if (referrer.search('orderMessages') != -1 || uri.search('orderMessages') != -1 || item === 'orderMessages') {
                                $('#button-general').removeClass('active');
                                $('#button-tasks').removeClass('active');
                                $('#button-messages').addClass('active');
                                $('#button-payments').removeClass('active');
                                $('#button-packages').removeClass('active');
                                $('#submit').hide();
                                $('#submitOrder').hide();
                                general.hide();
                                tasks.hide();
                                payments.hide();
                                packages.hide();
                                messages.show();
                                createButtonOrderPayments.hide();
                                createButtonOrderPackages.hide();
                                createButtonOrderTasks.hide();
                                pageTitle.removeClass();
                                pageTitle.addClass('voyager-chat');

                                breadcrumb.children().last().remove();
                                breadcrumb.append("<li class='active'><a href='/admin/orders/{{$order->id}}/edit#employees'>Zadania</a></li>");
                                addOrder.hide();
                            }
                            $('[name="change-button-form"]').on('click', function () {
                                value = this.value;
                                $('#' + value).show();
                                if (value === 'general') {
                                    $('#button-general').addClass('active');
                                    $('#button-tasks').removeClass('active');
                                    $('#button-messages').removeClass('active');
                                    $('#button-payments').removeClass('active');
                                    $('#button-packages').removeClass('active');
                                    general.show();
                                    tasks.hide();
                                    payments.hide();
                                    packages.hide();
                                    messages.hide();
                                    status.hide();
                                    $('#submit').show();
                                    $('#submitOrder').show();
                                    pageTitle.removeClass();
                                    pageTitle.addClass('voyager-file-text');
                                    createButtonOrderPayments.hide();
                                    createButtonOrderPackages.hide();
                                    createButtonOrderTasks.hide();
                                    breadcrumb.children().remove();
                                    breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
                                    breadcrumb.append("<li class='active'><a href='/admin/orders/{{$order->id}}/edit'>Zamówienia</a></li>");
                                    breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
                                    addOrder.hide();
                                } else if (value === 'tasks') {
                                    $('#button-general').removeClass('active');
                                    $('#button-tasks').addClass('active');
                                    $('#button-messages').removeClass('active');
                                    $('#button-payments').removeClass('active');
                                    $('#button-packages').removeClass('active');
                                    $('#submit').hide();
                                    $('#submitOrder').hide();
                                    general.hide();
                                    tasks.show();
                                    payments.hide();
                                    packages.hide();
                                    messages.hide();
                                    status.hide();
                                    pageTitle.removeClass();
                                    pageTitle.addClass('voyager-calendar');
                                    createButtonOrderPayments.hide();
                                    createButtonOrderPackages.hide();
                                    createButtonOrderTasks.show();
                                    breadcrumb.children().last().remove();
                                    breadcrumb.append("<li class='active'><a href='/admin/orders/{{$order->id}}/edit#order-tasks'>Zadania</a></li>");
                                    addOrder.hide();
                                } else if (value === 'payments') {
                                    $('#button-general').removeClass('active');
                                    $('#button-tasks').removeClass('active');
                                    $('#button-messages').removeClass('active');
                                    $('#button-payments').addClass('active');
                                    $('#button-packages').removeClass('active');
                                    $('#submit').hide();
                                    $('#submitOrder').hide();
                                    general.hide();
                                    tasks.hide();
                                    payments.show();
                                    packages.hide();
                                    messages.hide();
                                    status.hide();
                                    pageTitle.removeClass();
                                    pageTitle.addClass('voyager-wallet');
                                    createButtonOrderPayments.show();
                                    createButtonOrderPackages.hide();
                                    createButtonOrderTasks.hide();

                                    breadcrumb.children().last().remove();
                                    breadcrumb.append("<li class='active'><a href='/admin/orders/{{$order->id}}/edit#order-payments'>Płatności</a></li>");
                                    addOrder.hide();

                                } else if (value === 'messages') {
                                    $('#button-general').removeClass('active');
                                    $('#button-tasks').removeClass('active');
                                    $('#button-messages').addClass('active');
                                    $('#button-payments').removeClass('active');
                                    $('#button-packages').removeClass('active');
                                    $('#submit').hide();
                                    $('#submitOrder').hide();
                                    general.hide();
                                    tasks.hide();
                                    payments.hide();
                                    packages.hide();
                                    messages.show();
                                    status.hide();
                                    pageTitle.removeClass();
                                    pageTitle.addClass('voyager-chat');
                                    createButtonOrderPayments.hide();
                                    createButtonOrderPackages.hide();
                                    createButtonOrderTasks.hide();

                                    breadcrumb.children().last().remove();
                                    breadcrumb.append("<li class='active'><a href='/admin/orders/{{$order->id}}/edit#order-messages'>Wiadomości</a></li>");
                                    addOrder.hide();
                                } else if (value === 'packages') {
                                    $('#button-general').removeClass('active');
                                    $('#button-tasks').removeClass('active');
                                    $('#button-messages').removeClass('active');
                                    $('#button-payments').removeClass('active');
                                    $('#button-packages').addClass('active');
                                    $('#submit').hide();
                                    $('#submitOrder').hide();
                                    general.hide();
                                    tasks.hide();
                                    payments.hide();
                                    packages.show();
                                    messages.hide();
                                    status.hide();
                                    pageTitle.removeClass();
                                    pageTitle.addClass('voyager-archive');
                                    createButtonOrderPayments.hide();
                                    createButtonOrderPackages.show();
                                    createButtonOrderTasks.hide();
                                    breadcrumb.children().last().remove();
                                    breadcrumb.append("<li class='active'><a href='/admin/orders/{{$order->id}}/edit#order-packages'>Paczki</a></li>");
                                    addOrder.hide();
                                } else if(value === 'status'){
                                    $('#button-general').removeClass('active');
                                    $('#button-tasks').removeClass('active');
                                    $('#button-messages').removeClass('active');
                                    $('#button-payments').removeClass('active');
                                    $('#button-packages').removeClass('active');
                                    $('#submit').show();
                                    $('#submitOrder').hide();
                                    general.hide();
                                    tasks.hide();
                                    payments.hide();
                                    packages.hide();
                                    messages.hide();
                                    status.show();
                                    pageTitle.removeClass();
                                    pageTitle.addClass('voyager-tag');
                                    createButtonOrderPayments.hide();
                                    createButtonOrderPackages.hide();
                                    createButtonOrderTasks.hide();
                                    breadcrumb.children().last().remove();
                                    breadcrumb.append("<li class='active'><a href='/admin/orders/{{$order->id}}/edit#status'>Status zamówienia</a></li>");
                                    addOrder.hide();
                                }
                            });

                        });

                    </script>
                    <script>
                        const deleteRecordOrderPayments = (id) =>{
                            $('#delete_form')[0].action = "/admin/orderPayments/" + id;
                            $('#delete_modal').modal('show');
                        };
                        $.fn.dataTable.ext.errMode = 'throw';
                        // DataTable
                        let tableOrderPayments = $('#dataTableOrderPayments').DataTable({
                            language: {!! json_encode( __('voyager.datatable'), true) !!},
                            processing: true,
                            serverSide: true,
                            columnDefs: [
                                {className: "dt-center", targets: "_all"}
                            ],
                            order: [[0, "asc"]],
                            ajax: '{!! route('order_payments.datatable', ['id' => $order->id ]) !!}',
                            dom: 'Bfrtip',
                            buttons: [
                                {extend: 'colvis', text : 'Widzialność kolumn'}
                            ],
                            columns: [
                                {
                                    data: 'id',
                                    name: 'id',
                                    render: function (id, type, row) {
                                        console.log(row);
                                        return '<input type="checkbox">';
                                    }
                                },
                                {
                                    data: 'id',
                                    name: 'id'
                                },
                                {
                                    data: 'order_id',
                                    name: 'order_id'
                                },
                                {
                                    data: 'amount',
                                    name: 'amount'
                                },
                                {
                                    data: 'notices',
                                    name: 'notices'
                                },
                                {
                                    data: 'promise',
                                    name: 'promise',
                                    render: function (promise) {
                                        if(promise == '1') {
                                            return '<span>Tak</span>';
                                        } else {
                                            return '<span>Nie</span>';
                                        }
                                    }

                                },
                                {
                                    data: 'promise_date',
                                    name: 'promise_date'
                                },
                                {
                                    data: 'created_at',
                                    name: 'created_at'
                                },
                                {
                                    data: 'id',
                                    name: 'id',
                                    render: function (id) {
                                        let html = '<a href="/admin/orderPayments/' + id + '/edit" class="btn btn-sm btn-primary edit">';
                                        html += '<i class="voyager-edit"></i>';
                                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>';
                                        html += '</a>';

                                        html += '<button class="btn btn-sm btn-danger delete delete-record" onclick="deleteRecordOrderPayments(' + id + ')">';
                                        html += '<i class="voyager-trash"></i>';
                                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>';
                                        html += '</button>';
                                        return html;
                                    }
                                }
                            ]
                        });
                                @foreach($visibilitiesPayments as $key =>$row)

                        var {{'show'.$row->name}}  = @json($row->show);
                        {{'show'.$row->name}} = {{'show'.$row->name}}.map(function(x){
                            if (typeof tableOrderPayments.column(x+':name').index() === "number")
                                return tableOrderPayments.column(x+':name').index();
                        });
                        {{'show'.$row->name}} = {{'show'.$row->name}}.filter(function (el) {
                            return el != null;
                        });

                        var {{'hidden'.$row->name}} = @json($row->hidden);
                        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.map(function(x){
                            if (typeof tableOrderPayments.column(x+':name').index() === "number")
                                return tableOrderPayments.column(x+':name').index();
                        });
                        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.filter(function (el) {
                            return el != null;
                        });

                        tableOrderPayments.button().add({{1+$key}},{
                            extend: 'colvisGroup',
                            text: '{{$row->display_name}}',
                            show: {{'show'.$row->name}},
                            hide: {{'hidden'.$row->name}}
                        });
                        @endforeach




                        $('#dataTableOrderPayments thead tr th').each(function (i) {
                            var title = $(this).text();
                            if (title !== '' && title !== 'Akcje') {
                                $(this).html('<div><span>'+title+'</span></div><div><input type="text" placeholder="Szukaj '+ title +'" id="columnSearch' + i + '"/></div>');
                            } else if(title == 'Akcje') {
                                $(this).html('<span id="columnSearch' + i + '">Akcje</span>');
                            }
                            $('input', this).on('keyup change', function () {
                                if (table.column(i).search() !== this.value) {
                                    table
                                        .column(i)
                                        .search(this.value)
                                        .draw();
                                }
                            });
                        });


                        const deleteRecordOrderTasks = (id) =>{
                            $('#delete_form')[0].action = "/admin/orderTasks/" + id;
                            $('#delete_modal').modal('show');
                        };
                        $.fn.dataTable.ext.errMode = 'throw';
                        // DataTable

                        let tableOrderTasks = $('#dataTableOrderTasks').DataTable({
                            language: {!! json_encode( __('voyager.datatable'), true) !!},
                            processing: true,
                            serverSide: true,
                            columnDefs: [
                                {className: "dt-center", targets: "_all"}
                            ],
                            order: [[0, "asc"]],
                            ajax: '{!! route('order_tasks.datatable', ['id' => $order->id]) !!}',
                            dom: 'Bfrtip',
                            buttons: [
                                {extend: 'colvis', text : 'Widzialność kolumn'}
                            ],
                            columns: [
                                {
                                    data: 'id',
                                    name: 'id',
                                    render: function (id) {
                                        return '<input type="checkbox">';
                                    }
                                },
                                {
                                    data: 'id',
                                    name: 'id'
                                },
                                {
                                    data: 'description',
                                    name: 'description'
                                },
                                {
                                    data: 'title',
                                    name: 'title'
                                },
                                {
                                    data: 'show_label_at',
                                    name: 'show_label_at'
                                },
                                {
                                    data: 'status',
                                    name: 'status',
                                    render: function(status) {
                                        if (status === 'OPEN') {
                                            return '<span>' + {!! json_encode(__('order_tasks.table.open'), true) !!} + '</span>';
                                        } else {
                                            return '<span>' + {!! json_encode(__('order_tasks.table.closed'), true) !!} + '</span>';
                                        }
                                    }
                                },
                                {
                                    data: 'created_at',
                                    name: 'created_at'
                                },
                                {
                                    data: 'id',
                                    name: 'id',
                                    render: function (id) {
                                        let html = '<a href="/admin/orderTasks/' + id + '/edit" class="btn btn-sm btn-primary edit">';
                                        html += '<i class="voyager-edit"></i>';
                                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>';
                                        html += '</a>';

                                        html += '<button class="btn btn-sm btn-danger delete delete-record" onclick="deleteRecordOrderTasks(' + id + ')">';
                                        html += '<i class="voyager-trash"></i>';
                                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>';
                                        html += '</button>';
                                        return html;
                                    }
                                }
                            ]
                        });
                                @foreach($visibilitiesTask as $key =>$row)

                        var {{'show'.$row->name}}  = @json($row->show);
                        {{'show'.$row->name}} = {{'show'.$row->name}}.map(function(x){
                            if (typeof tableOrderTasks.column(x+':name').index() === "number")
                                return tableOrderTasks.column(x+':name').index();
                        });
                        {{'show'.$row->name}} = {{'show'.$row->name}}.filter(function (el) {
                            return el != null;
                        });

                        var {{'hidden'.$row->name}} = @json($row->hidden);
                        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.map(function(x){
                            if (typeof tableOrderTasks.column(x+':name').index() === "number")
                                return tableOrderTasks.column(x+':name').index();
                        });
                        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.filter(function (el) {
                            return el != null;
                        });

                        tableOrderTasks.button().add({{1+$key}},{
                            extend: 'colvisGroup',
                            text: '{{$row->display_name}}',
                            show: {{'show'.$row->name}},
                            hide: {{'hidden'.$row->name}}
                        });
                        @endforeach

                        $('#dataTableOrderTasks thead tr th').each(function (i) {
                            var title = $(this).text();
                            if (title !== '' && title !== 'Akcje') {
                                $(this).html('<div><span>'+title+'</span></div><div><input type="text" placeholder="Szukaj '+ title +'" id="columnSearch' + i + '"/></div>');
                            } else if(title == 'Akcje') {
                                $(this).html('<span id="columnSearch' + i + '">Akcje</span>');
                            }
                            $('input', this).on('keyup change', function () {
                                if (table.column(i).search() !== this.value) {
                                    table
                                        .column(i)
                                        .search(this.value)
                                        .draw();
                                }
                            });
                        });

                        const deleteRecordOrderPackages = (id) =>{
                            $('#delete_form')[0].action = "/admin/orderPackages/" + id;
                            $('#delete_modal').modal('show');
                        };
                        $.fn.dataTable.ext.errMode = 'throw';
                        // DataTable
                        let tableOrderPackages = $('#dataTableOrderPackages').DataTable({
                            language: {!! json_encode( __('voyager.datatable'), true) !!},
                            processing: true,
                            serverSide: true,
                            columnDefs: [
                                {className: "dt-center", targets: "_all"}
                            ],
                            order: [[0, "asc"]],
                            ajax: '{!! route('order_packages.datatable', ['id' => $order->id]) !!}',
                            dom: 'Bfrtip',
                            buttons: [
                                {extend: 'colvis', text : 'Widzialność kolumn'}
                            ],
                            columns: [
                                {
                                    data: 'id',
                                    name: 'id',
                                    render: function (id) {
                                        return '<input type="checkbox">';
                                    }
                                },
                                {
                                    data: 'id',
                                    name: 'id',
                                    render: function (id, data, row) {
                                        let html = '';
                                        if(row.status !== 'SENDING' && row.status !== 'WAITING_FOR_SENDING' && row.status !== 'CANCELLED' && row.status !== 'WAITING_FOR_CANCELLED' && row.status !== 'DELIVERED' && row.service_courier_name !== 'GIELDA' && row.service_courier_name !== 'ODBIOR_OSOBISTY' && row.delivery_courier_name !== 'GIELDA' && row.delivery_courier_name !== 'ODBIOR_OSOBISTY') {
                                            html += '<button class="btn btn-sm btn-success edit" onclick="sendPackage(' + id + ',' + row.order_id + ')">';
                                            html += '<i class="voyager-mail"></i>';
                                            html += '<span class="hidden-xs hidden-sm"> Wyślij</span>';
                                            html += '</button>';
                                        }
                                        html += '<a href="/admin/orderPackages/' + id + '/edit" class="btn btn-sm btn-primary edit">';
                                        html += '<i class="voyager-edit"></i>';
                                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>';
                                        html += '</a>';
                                        html += '<button class="btn btn-sm btn-danger delete delete-record" onclick="deleteRecordOrderPackages(' + id + ')">';
                                        html += '<i class="voyager-trash"></i>';
                                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>';
                                        html += '</button>';

                                        return html;
                                    }
                                },
                                {
                                    data: 'id',
                                    name: 'id'
                                },
                                {
                                    data: 'number',
                                    name: 'number'
                                },
                                {
                                    data: 'status',
                                    name: 'status',
                                    render: function (status){
                                        if (status === 'DELIVERED') {
                                            return '<span style="color: green;">' + {!! json_encode(__('order_packages.table.delivered'), true) !!} + '</span>';
                                        } else if(status === 'CANCELLED') {
                                            return '<span style="color: red;">' + {!! json_encode(__('order_packages.table.cancelled'), true) !!} + '</span>';
                                        } else if(status === 'NEW') {
                                            return '<span style="color: blue;">' + {!! json_encode(__('order_packages.table.new'), true) !!} + '</span>';
                                        } else if(status === 'SENDING') {
                                            return '<span style="color: orange;">' + {!! json_encode(__('order_packages.table.sending'), true) !!} + '</span>';
                                        } else if(status === 'WAITING_FOR_SENDING') {
                                            return '<span style="color: orange;">' + {!! json_encode(__('order_packages.table.waiting_for_sending'), true) !!} + '</span>';
                                        } else if(status === 'WAITING_FOR_CANCELLED') {

                                            return '<span style="color: orange;">' + {!! json_encode(__('order_packages.table.waiting_for_cancelled'), true) !!} + '</span>';
                                        } else if(status === 'REJECT_CANCELLED') {

                                            return '<span style="color: red;">Anulacja odrzucona</span>';
                                        }
                                    }
                                },
                                {
                                    data: 'letter_number',
                                    name: 'letter_number',
                                },
                                {
                                    data: 'size_a',
                                    name: 'size_a'
                                },
                                {
                                    data: 'size_b',
                                    name: 'size_b'
                                },
                                {
                                    data: 'size_c',
                                    name: 'size_c'
                                },
                                {
                                    data: 'weight',
                                    name: 'weight'
                                },
                                {
                                    data: 'container_type',
                                    name: 'container_type'
                                },
                                {
                                    data: 'shape',
                                    name: 'shape'
                                },
                                {
                                    data: 'cash_on_delivery',
                                    name: 'cash_on_delivery',
                                },
                                {
                                    data: 'notices',
                                    name: 'notices'
                                },
                                {
                                    data: 'delivery_courier_name',
                                    name: 'delivery_courier_name'
                                },
                                {
                                    data: 'service_courier_name',
                                    name: 'service_courier_name'
                                },
                                {
                                    data: 'shipment_date',
                                    name: 'shipment_date'
                                },
                                {
                                    data: 'delivery_date',
                                    name: 'delivery_date'
                                },
                                {
                                    data: 'cost_for_client',
                                    name: 'cost_for_client',
                                },
                                {
                                    data: 'cost_for_company',
                                    name: 'cost_for_company',
                                },
                                {
                                    data: 'real_cost_for_company',
                                    name: 'real_cost_for_company'
                                },

                                {
                                    data: 'sending_number',
                                    name: 'sending_number'
                                },
                                {
                                    data: 'quantity',
                                    name: 'quantity'
                                },
                                {
                                    data: 'created_at',
                                    name: 'created_at',
                                },
                            ]
                        });
                                @foreach($visibilitiesPackage as $key =>$row)

                        var {{'show'.$row->name}}  = @json($row->show);
                        {{'show'.$row->name}} = {{'show'.$row->name}}.map(function(x){
                            if (typeof tableOrderPackages.column(x+':name').index() === "number")
                                return tableOrderPackages.column(x+':name').index();
                        });
                        {{'show'.$row->name}} = {{'show'.$row->name}}.filter(function (el) {
                            return el != null;
                        });

                        var {{'hidden'.$row->name}} = @json($row->hidden);
                        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.map(function(x){
                            if (typeof tableOrderPackages.column(x+':name').index() === "number")
                                return tableOrderPackages.column(x+':name').index();
                        });
                        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.filter(function (el) {
                            return el != null;
                        });

                        tableOrderPackages.button().add({{1+$key}},{
                            extend: 'colvisGroup',
                            text: '{{$row->display_name}}',
                            show: {{'show'.$row->name}},
                            hide: {{'hidden'.$row->name}}
                        });
                        @endforeach

                        $('#dataTableOrderPackages thead tr th').each(function (i) {
                            var title = $(this).text();
                            if (title !== '' && title !== 'Akcje') {
                                $(this).html('<div><span>'+title+'</span></div><div><input type="text" placeholder="Szukaj '+ title +'" id="columnSearch' + i + '"/></div>');
                            } else if(title == 'Akcje') {
                                $(this).html('<span id="columnSearch' + i + '">Akcje</span>');
                            }
                            $('input', this).on('keyup change', function () {
                                if (table.column(i).search() !== this.value) {
                                    table
                                        .column(i)
                                        .search(this.value)
                                        .draw();
                                }
                            });
                        });

                        function sendPackage(id, orderId){
                            $('#order_courier > div > div > div.modal-header > h4 > span').remove();
                            $('#order_courier > div > div > div.modal-header > span').remove();
                            $.ajax({
                                url: '/admin/orders/'+orderId+'/package/'+id+'/send',
                            }).done(function(data) {
                                $('#order_courier').modal('show');
                                if(data.message == 'Kurier zostanie zamówiony w przeciągu kilku minut' || data.message === null) {
                                    $('#order_courier > div > div > div.modal-header > h4').append('<span>Kurier zostanie zamówiony w przeciągu kilku minut</span>');
                                } else {
                                    $('#order_courier > div > div > div.modal-header > h4').append('<span>Jedno z wymaganych pól nie zostało zdefiniowane:</span>');
                                    $('#order_courier > div > div > div.modal-header').append('<span style="color:red;">'+data.message+'</span><br>');
                                }
                                $('#success-ok').on('click', function() {
                                    window.location.href = '/admin/orders/' + orderId + '/edit';
                                });
                            }).fail(function() {
                                $('#order_courier_problem').modal('show');
                                $('#problem-ok').on('click', function() {
                                    window.location.href = '/admin/orders/' + orderId + '/edit';
                                });
                            });
                        }
                        $(document).on("change","select",function(){
                            $("option[value=" + this.value + "]", this)
                                .attr("selected", true).siblings()
                                .removeAttr("selected")
                        });
                        $( document ).ready(function() {

                            var editor;
                            ClassicEditor
                                .create( document.querySelector( '.editor' ) )
                                .then( editor => {
                                console.log( editor );
                            $('#status').on('change', function(){
                                $('#status option').each(function(){
                                    if ($(this).is(':selected')) {
                                        $.ajax({
                                            url: '/admin/orders/status/'+ this.value +'/message',
                                            success: function(result){
                                                editor.data.set(result);
                                            }
                                        });
                                    }
                                });
                            });
                            $('#status option').each(function(){
                                if ($(this).is(':selected')) {
                                    $.ajax({
                                        url: '/admin/orders/status/'+ this.value +'/message',
                                        success: function(result){
                                            editor.data.set(result);
                                        }
                                    });
                                }
                            });
                        } )
                        .catch( error => {
                                console.error( error );
                        }
                        );

                            function updateProfit() {
                                commaReplace('.priceChange');
                                var gross_purchase_price_sum = 0;
                                var net_purchase_price_sum = 0;
                                var gross_selling_price_sum = 0;
                                var net_selling_price_sum = 0;
                                console.log('ppp');
                                $('.productsTableEdit .gross_purchase_price_commercial_unit').each(function(){
                                    var quantity = parseFloat($('[name="quantity_commercial['+$(this).data('item-id')+']"').val());
                                    var gross_selling_price = $('.gross_selling_price_commercial_unit[data-item-id="'+$(this).data('item-id')+'"]').val();
                                    gross_purchase_price_sum += $(this).val() * quantity;
                                    var gross_purchase_price = $(this).val() * quantity;
                                    var sellingSum = gross_selling_price * quantity;
                                    var weightSum = parseFloat($('[name="weight_trade_unit[' + $(this).data('item-id') + ']').val()) * quantity;
                                    var profitItem = ((gross_selling_price * quantity ) - gross_purchase_price).toFixed(2);

                                    var unitCommercialName = $('[name="unit_commercial_name[' + $(this).data('item-id') + ']"]').val();

                                    var unitBasicUnits = $('[name="unit_basic_units[' + $(this).data('item-id') + ']"]').val();
                                    var unitBasicName = $('[name="unit_basic_name[' + $(this).data('item-id') + ']"]').val();

                                    var unitCalculationUnits = $('[name="calculation_unit_units[' + $(this).data('item-id') + ']"]').val();
                                    var unitCalculationConsumption = $('[name="calculation_unit_consumption[' + $(this).data('item-id') + ']"]').val();
                                    var unitCalculationName = $('[name="calculation_unit_name[' + $(this).data('item-id') + ']"]').val();

                                    var unitCollectiveUnits = $('[name="unit_of_collective_units[' + $(this).data('item-id') + ']"]').val();
                                    var unitCollectiveName = $('[name="unit_of_collective_name[' + $(this).data('item-id') + ']"]').val();

                                    $('[name="unit_commercial[' + $(this).data('item-id') + ']"]').val(quantity + ' ' + unitCommercialName);
                                    $('[name="unit_basic[' + $(this).data('item-id') + ']"]').val(quantity * unitBasicUnits + ' ' + unitBasicName);
                                    $('[name="calculation_unit[' + $(this).data('item-id') + ']"]').val((quantity * unitCalculationUnits)/unitCalculationConsumption + ' ' + unitCalculationName);
                                    $('[name="unit_of_collective[' + $(this).data('item-id') + ']"]').val((quantity * unitCollectiveUnits).toFixed(4) + ' ' + unitCollectiveName);
                                    $('input.item-weight[data-item-id="'+$(this).data('item-id')+'"]').val(weightSum + ' kg');
                                    $('input.item-profit[data-item-id="'+$(this).data('item-id')+'"]').val(profitItem + ' zł');
                                    $('input.item-value[data-item-id="'+$(this).data('item-id')+'"]').val(sellingSum + ' zł');
                                });
                                $('.productsTableEdit .net_purchase_price_commercial_unit').each(function(){
                                    var quantity = parseFloat($('[name="quantity_commercial['+$(this).data('item-id')+']"').val());
                                    net_purchase_price_sum += $(this).val() * quantity;
                                });
                                $('.productsTableEdit .gross_selling_price_commercial_unit').each(function(){
                                    var quantity = parseFloat($('[name="quantity_commercial['+$(this).data('item-id')+']"').val());
                                    gross_selling_price_sum += $(this).val() * quantity;
                                });
                                $('.productsTableEdit .net_selling_price_commercial_unit').each(function(){
                                    var quantity = parseFloat($('[name="quantity_commercial['+$(this).data('item-id')+']"').val());
                                    net_selling_price_sum += $(this).val() * quantity;
                                });
                                if($('#additional_service_cost').val() == '') {
                                    additionalServiceCost = 0;
                                } else {
                                    additionalServiceCost = parseFloat($('#additional_service_cost').val());
                                }
                                var profit = ((gross_selling_price_sum - gross_purchase_price_sum)).toFixed(2);
                                var total_price = gross_selling_price_sum.toFixed(2);
                                console.log('Total ' + total_price);
                                $('input#profit').val(profit);
                                $('input#total_price').val(total_price);
                                $("#profitInfo").val($('#profit').val());
                                $("#totalPriceInfo").val($("#total_price").val());
                                $("#orderItemsSum").val(total_price);
				updateOrderSum();
                                $("#weightInfo").val($("#weight").val());
                            }
                            $('.quantityChange').on('change', function() {
                                updateOrderSum();
                            });
                            $('#additional_service_cost').on('change', function() {
                                updateOrderSum(1);
                                commaReplace('.priceChange');
                            });

                            function updateOnStart() {
                                console.log('b');
                                commaReplace('.priceChange');

                                var itemId = $(this).data('item-id');

                                var net_purchase_price_commercial_unit = $('.net_purchase_price_commercial_unit[data-item-id="'+itemId+'"');
                                var net_purchase_price_basic_unit = $('.net_purchase_price_basic_unit[data-item-id="'+itemId+'"');
                                var net_purchase_price_calculated_unit = $('.net_purchase_price_calculated_unit[data-item-id="'+itemId+'"');
                                var net_purchase_price_aggregate_unit = $('.net_purchase_price_aggregate_unit[data-item-id="'+itemId+'"');
                                var net_selling_price_commercial_unit = $('.net_selling_price_commercial_unit[data-item-id="'+itemId+'"');
                                var net_selling_price_basic_unit = $('.net_selling_price_basic_unit[data-item-id="'+itemId+'"');
                                var net_selling_price_calculated_unit = $('.net_selling_price_calculated_unit[data-item-id="'+itemId+'"');
                                var net_selling_price_aggregate_unit = $('.net_selling_price_aggregate_unit[data-item-id="'+itemId+'"');

                                var gross_purchase_price_commercial_unit = $('.gross_purchase_price_commercial_unit[data-item-id="'+itemId+'"');
                                var gross_purchase_price_basic_unit = $('.gross_purchase_price_basic_unit[data-item-id="'+itemId+'"');
                                var gross_purchase_price_calculated_unit = $('.gross_purchase_price_calculated_unit[data-item-id="'+itemId+'"');
                                var gross_purchase_price_aggregate_unit = $('.gross_purchase_price_aggregate_unit[data-item-id="'+itemId+'"');
                                var gross_selling_price_commercial_unit = $('.gross_selling_price_commercial_unit[data-item-id="'+itemId+'"');
                                var gross_selling_price_basic_unit = $('.gross_selling_price_basic_unit[data-item-id="'+itemId+'"');
                                var gross_selling_price_calculated_unit = $('.gross_selling_price_calculated_unit[data-item-id="'+itemId+'"');
                                var gross_selling_price_aggregate_unit = $('.gross_selling_price_aggregate_unit[data-item-id="'+itemId+'"');

                                var numbers_of_basic_commercial_units_in_pack = $('.numbers_of_basic_commercial_units_in_pack[data-item-id="'+itemId+'"');
                                var number_of_sale_units_in_the_pack = $('.number_of_sale_units_in_the_pack[data-item-id="'+itemId+'"');
                                var number_of_trade_items_in_the_largest_unit = $('.number_of_trade_items_in_the_largest_unit[data-item-id="'+itemId+'"');
                                var unit_consumption = $('.unit_consumption[data-item-id="'+itemId+'"');

                                var net_purchase_price_commercial_unit_value = parseFloat(net_purchase_price_commercial_unit.val());
                                var net_purchase_price_basic_unit_value = parseFloat(net_purchase_price_basic_unit.val());
                                var net_purchase_price_calculated_unit_value = parseFloat(net_purchase_price_calculated_unit.val());
                                var net_purchase_price_aggregate_unit_value = parseFloat(net_purchase_price_aggregate_unit.val());
                                var net_selling_price_commercial_unit_value = parseFloat(net_selling_price_commercial_unit.val());
                                var net_selling_price_basic_unit_value = parseFloat(net_selling_price_basic_unit.val());
                                var net_selling_price_calculated_unit_value = parseFloat(net_selling_price_calculated_unit.val());
                                var net_selling_price_aggregate_unit_value = parseFloat(net_selling_price_aggregate_unit.val());

                                var gross_purchase_price_commercial_unit_value = parseFloat(gross_purchase_price_commercial_unit.val());
                                var gross_purchase_price_basic_unit_value = parseFloat(gross_purchase_price_basic_unit.val());
                                var gross_purchase_price_calculated_unit_value = parseFloat(gross_purchase_price_calculated_unit.val());
                                var gross_purchase_price_aggregate_unit_value = parseFloat(gross_purchase_price_aggregate_unit.val());
                                var gross_selling_price_commercial_unit_value = parseFloat(gross_selling_price_commercial_unit.val());
                                var gross_selling_price_basic_unit_value = parseFloat(gross_selling_price_basic_unit.val());
                                var gross_selling_price_calculated_unit_value = parseFloat(gross_selling_price_calculated_unit.val());
                                var gross_selling_price_aggregate_unit_value = parseFloat(gross_selling_price_aggregate_unit.val());

                                var numbers_of_basic_commercial_units_in_pack_value = parseFloat(numbers_of_basic_commercial_units_in_pack.val());
                                var number_of_sale_units_in_the_pack_value = parseFloat(number_of_sale_units_in_the_pack.val());
                                var number_of_trade_items_in_the_largest_unit_value =parseFloat(number_of_trade_items_in_the_largest_unit.val());
                                var unit_consumption_value = parseFloat(unit_consumption.val());
                                var values = {
                                    'net_purchase_price_commercial_unit' :
                                        {
                                            'net_purchase_price_aggregate_unit' : (net_purchase_price_commercial_unit_value * number_of_sale_units_in_the_pack_value).toFixed(4),
                                            'net_purchase_price_basic_unit' : (net_purchase_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value).toFixed(4),
                                            'net_purchase_price_calculated_unit' : ((net_purchase_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * unit_consumption_value).toFixed(4),
                                            'gross_purchase_price_aggregate_unit' : ((net_purchase_price_commercial_unit_value * number_of_sale_units_in_the_pack_value) * 1.23).toFixed(4),
                                            'gross_purchase_price_basic_unit' : ((net_purchase_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * 1.23).toFixed(4),
                                            'gross_purchase_price_calculated_unit' : (((net_purchase_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * unit_consumption_value)  * 1.23).toFixed(4),
                                        },
                                    'net_purchase_price_basic_unit' :
                                        {
                                            'net_purchase_price_aggregate_unit' : (net_purchase_price_basic_unit_value * number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value).toFixed(4),
                                            'net_purchase_price_commercial_unit' : (net_purchase_price_basic_unit_value * numbers_of_basic_commercial_units_in_pack_value).toFixed(2),
                                            'net_purchase_price_calculated_unit' : ((net_purchase_price_basic_unit_value * unit_consumption_value)).toFixed(4),
                                            'gross_purchase_price_aggregate_unit' :  ((net_purchase_price_basic_unit_value * number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value * 1.23)).toFixed(4),
                                            'gross_purchase_price_commercial_unit' : ((net_purchase_price_basic_unit_value * numbers_of_basic_commercial_units_in_pack_value)  * 1.23).toFixed(2),
                                            'gross_purchase_price_calculated_unit' :  (((net_purchase_price_basic_unit_value * unit_consumption_value) * 1.23)).toFixed(4)
                                        },
                                    'net_purchase_price_aggregate_unit' :
                                        {
                                            'net_purchase_price_basic_unit' : (net_purchase_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value)).toFixed(4),
                                            'net_purchase_price_commercial_unit' : (net_purchase_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value)).toFixed(2),
                                            'net_purchase_price_calculated_unit' : (net_purchase_price_aggregate_unit_value * (number_of_sale_units_in_the_pack_value)).toFixed(4),
                                            'gross_purchase_price_basic_unit' : ((net_purchase_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value)) * 1.23).toFixed(4),
                                            'gross_purchase_price_commercial_unit' :  ((net_purchase_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(2),
                                            'gross_purchase_price_calculated_unit' : ((net_purchase_price_aggregate_unit_value * (number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(4),
                                        },
                                    'net_purchase_price_calculated_unit' :
                                        {
                                            'net_purchase_price_basic_unit' : ((net_purchase_price_calculated_unit_value / unit_consumption_value)).toFixed(4),
                                            'net_purchase_price_commercial_unit' : ((net_purchase_price_calculated_unit_value / unit_consumption_value) * numbers_of_basic_commercial_units_in_pack_value).toFixed(2),
                                            'net_purchase_price_aggregate_unit' : (((net_purchase_price_calculated_unit_value / number_of_sale_units_in_the_pack_value))).toFixed(4),
                                            'gross_purchase_price_basic_unit' : (((net_purchase_price_calculated_unit_value / unit_consumption_value)) * 1.23 ).toFixed(4),
                                            'gross_purchase_price_commercial_unit' : (((net_purchase_price_calculated_unit_value / unit_consumption_value) * numbers_of_basic_commercial_units_in_pack_value) * 1.23 ).toFixed(2),
                                            'gross_purchase_price_aggregate_unit' : (((net_purchase_price_calculated_unit_value / number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(4),

                                        },
                                    'net_selling_price_commercial_unit' :
                                        {
                                            'net_selling_price_aggregate_unit' : (net_selling_price_commercial_unit_value * number_of_sale_units_in_the_pack_value).toFixed(4),
                                            'net_selling_price_basic_unit' : (net_selling_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value).toFixed(4),
                                            'net_selling_price_calculated_unit' :  ((net_selling_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * unit_consumption_value).toFixed(4),
                                            'gross_selling_price_aggregate_unit' : ((net_selling_price_commercial_unit_value * number_of_sale_units_in_the_pack_value) * 1.23).toFixed(4),
                                            'gross_selling_price_basic_unit' : ((net_selling_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * 1.23).toFixed(4),
                                            'gross_selling_price_calculated_unit' :  (((net_selling_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * unit_consumption_value) * 1.23).toFixed(4),
                                        },
                                    'net_selling_price_basic_unit' :
                                        {
                                            'net_selling_price_aggregate_unit' : (net_selling_price_basic_unit_value * number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value).toFixed(2),
                                            'net_selling_price_commercial_unit' : (net_selling_price_basic_unit_value * numbers_of_basic_commercial_units_in_pack_value).toFixed(2),
                                            'net_selling_price_calculated_unit' : ((net_selling_price_basic_unit_value * unit_consumption_value)).toFixed(2),
                                            'gross_selling_price_aggregate_unit' :  ((net_selling_price_basic_unit_value * number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value * 1.23)).toFixed(2),
                                            'gross_selling_price_commercial_unit' : ((net_selling_price_basic_unit_value * numbers_of_basic_commercial_units_in_pack_value)  * 1.23).toFixed(2),
                                            'gross_selling_price_calculated_unit' :  (((net_selling_price_basic_unit_value * unit_consumption_value) * 1.23)).toFixed(2)
                                        },
                                    'net_selling_price_aggregate_unit' :
                                        {
                                            'net_selling_price_basic_unit' : (net_selling_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value)).toFixed(2),
                                            'net_selling_price_commercial_unit' : (net_selling_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value)).toFixed(2),
                                            'net_selling_price_calculated_unit' : (net_selling_price_aggregate_unit_value * (number_of_sale_units_in_the_pack_value)).toFixed(2),
                                            'gross_selling_price_basic_unit' : ((net_selling_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value)) * 1.23).toFixed(2),
                                            'gross_selling_price_commercial_unit' :  ((net_selling_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(2),
                                            'gross_selling_price_calculated_unit' : ((net_selling_price_aggregate_unit_value * (number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(2),
                                        },
                                    'net_selling_price_calculated_unit' :
                                        {
                                            'net_selling_price_basic_unit' : ((net_selling_price_calculated_unit_value / unit_consumption_value)).toFixed(2),
                                            'net_selling_price_commercial_unit' : ((net_selling_price_calculated_unit_value / unit_consumption_value) * numbers_of_basic_commercial_units_in_pack_value).toFixed(2),
                                            'net_selling_price_aggregate_unit' : ((net_selling_price_calculated_unit_value / (number_of_sale_units_in_the_pack_value))).toFixed(2),
                                            'gross_selling_price_basic_unit' : (((net_selling_price_calculated_unit_value / unit_consumption_value)) * 1.23 ).toFixed(2),
                                            'gross_selling_price_commercial_unit' : (((net_selling_price_calculated_unit_value / unit_consumption_value) * numbers_of_basic_commercial_units_in_pack_value) * 1.23 ).toFixed(2),
                                            'gross_selling_price_aggregate_unit' : ((net_selling_price_calculated_unit_value / (number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(2),

                                        }
                                }

                                if ($(this).hasClass('net_purchase_price_commercial_unit')) {
                                    net_purchase_price_aggregate_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_aggregate_unit']);
                                    net_purchase_price_basic_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_basic_unit']);
                                    net_purchase_price_calculated_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_calculated_unit']);
                                    gross_purchase_price_commercial_unit.val(($(this).val() * 1.23).toFixed(2));
                                    gross_purchase_price_aggregate_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_aggregate_unit']);
                                    gross_purchase_price_basic_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_basic_unit']);
                                    gross_purchase_price_calculated_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_calculated_unit']);
                                }

                                if ($(this).hasClass('net_purchase_price_basic_unit')) {
                                    net_purchase_price_aggregate_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_aggregate_unit']);
                                    net_purchase_price_commercial_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_commercial_unit']);
                                    net_purchase_price_calculated_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_calculated_unit']);
                                    gross_purchase_price_basic_unit.val(($(this).val() * 1.23).toFixed(4));
                                    gross_purchase_price_aggregate_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_aggregate_unit']);
                                    gross_purchase_price_commercial_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_commercial_unit']);
                                    gross_purchase_price_calculated_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_calculated_unit']);
                                }

                                if ($(this).hasClass('net_purchase_price_aggregate_unit')) {
                                    net_purchase_price_basic_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_basic_unit']);
                                    net_purchase_price_commercial_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_commercial_unit']);
                                    net_purchase_price_calculated_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_calculated_unit']);
                                    gross_purchase_price_aggregate_unit.val(($(this).val() * 1.23).toFixed(4));
                                    gross_purchase_price_basic_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_basic_unit']);
                                    gross_purchase_price_commercial_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_commercial_unit']);
                                    gross_purchase_price_calculated_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_calculated_unit']);
                                }

                                if ($(this).hasClass('net_purchase_price_calculated_unit')) {
                                    net_purchase_price_basic_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_basic_unit']);
                                    net_purchase_price_commercial_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_commercial_unit']);
                                    net_purchase_price_aggregate_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_aggregate_unit']);
                                    gross_purchase_price_calculated_unit.val(($(this).val() * 1.23).toFixed(4));
                                    gross_purchase_price_basic_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_basic_unit']);
                                    gross_purchase_price_commercial_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_commercial_unit']);
                                    gross_purchase_price_aggregate_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_aggregate_unit']);
                                }

                                if ($(this).hasClass('gross_purchase_price_commercial_unit')) {
                                    net_purchase_price_aggregate_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_aggregate_unit']);
                                    net_purchase_price_basic_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_basic_unit']);
                                    net_purchase_price_calculated_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_calculated_unit']);
                                    net_purchase_price_commercial_unit.val(($(this).val() / 1.23).toFixed(2));
                                    gross_purchase_price_aggregate_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_aggregate_unit']);
                                    gross_purchase_price_basic_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_basic_unit']);
                                    gross_purchase_price_calculated_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_calculated_unit']);
                                }

                                if ($(this).hasClass('gross_purchase_price_basic_unit')) {
                                    net_purchase_price_aggregate_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_aggregate_unit']);
                                    net_purchase_price_commercial_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_commercial_unit']);
                                    net_purchase_price_calculated_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_calculated_unit']);
                                    net_purchase_price_basic_unit.val(($(this).val() / 1.23).toFixed(4));
                                    gross_purchase_price_aggregate_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_aggregate_unit']);
                                    gross_purchase_price_commercial_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_commercial_unit']);
                                    gross_purchase_price_calculated_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_calculated_unit']);
                                }

                                if ($(this).hasClass('gross_purchase_price_aggregate_unit')) {
                                    net_purchase_price_basic_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_basic_unit']);
                                    net_purchase_price_commercial_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_commercial_unit']);
                                    net_purchase_price_calculated_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_calculated_unit']);
                                    net_purchase_price_aggregate_unit.val(($(this).val() / 1.23).toFixed(4));
                                    gross_purchase_price_basic_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_basic_unit']);
                                    gross_purchase_price_commercial_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_commercial_unit']);
                                    gross_purchase_price_calculated_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_calculated_unit']);
                                }

                                if ($(this).hasClass('gross_purchase_price_calculated_unit')) {
                                    net_purchase_price_basic_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_basic_unit']);
                                    net_purchase_price_commercial_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_commercial_unit']);
                                    net_purchase_price_aggregate_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_aggregate_unit']);
                                    net_purchase_price_calculated_unit.val(($(this).val() / 1.23).toFixed(4));
                                    gross_purchase_price_basic_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_basic_unit']);
                                    gross_purchase_price_commercial_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_commercial_unit']);
                                    gross_purchase_price_aggregate_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_aggregate_unit']);
                                }

                                if ($(this).hasClass('net_selling_price_commercial_unit')) {
                                    net_selling_price_aggregate_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_aggregate_unit']);
                                    net_selling_price_basic_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_basic_unit']);
                                    net_selling_price_calculated_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_calculated_unit']);
                                    gross_selling_price_commercial_unit.val(($(this).val() * 1.23).toFixed(2));
                                    gross_selling_price_aggregate_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_aggregate_unit']);
                                    gross_selling_price_basic_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_basic_unit']);
                                    gross_selling_price_calculated_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_calculated_unit']);
                                }

                                if ($(this).hasClass('net_selling_price_basic_unit')) {
                                    net_selling_price_aggregate_unit.val(values['net_selling_price_basic_unit']['net_selling_price_aggregate_unit']);
                                    net_selling_price_commercial_unit.val(values['net_selling_price_basic_unit']['net_selling_price_commercial_unit']);
                                    net_selling_price_calculated_unit.val(values['net_selling_price_basic_unit']['net_selling_price_calculated_unit']);
                                    gross_selling_price_basic_unit.val(($(this).val() * 1.23).toFixed(4));
                                    gross_selling_price_aggregate_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_aggregate_unit']);
                                    gross_selling_price_commercial_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_commercial_unit']);
                                    gross_selling_price_calculated_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_calculated_unit']);
                                }

                                if ($(this).hasClass('net_selling_price_aggregate_unit')) {
                                    net_selling_price_basic_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_basic_unit']);
                                    net_selling_price_commercial_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_commercial_unit']);
                                    net_selling_price_calculated_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_calculated_unit']);
                                    gross_selling_price_aggregate_unit.val(($(this).val() * 1.23).toFixed(4));
                                    gross_selling_price_basic_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_basic_unit']);
                                    gross_selling_price_commercial_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_commercial_unit']);
                                    gross_selling_price_calculated_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_calculated_unit']);
                                }

                                if ($(this).hasClass('net_selling_price_calculated_unit')) {
                                    net_selling_price_basic_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_basic_unit']);
                                    net_selling_price_commercial_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_commercial_unit']);
                                    net_selling_price_aggregate_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_aggregate_unit']);
                                    gross_selling_price_calculated_unit.val(($(this).val() * 1.23).toFixed(4));
                                    gross_selling_price_basic_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_basic_unit']);
                                    gross_selling_price_commercial_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_commercial_unit']);
                                    gross_selling_price_aggregate_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_aggregate_unit']);
                                }

                                if ($(this).hasClass('gross_selling_price_commercial_unit')) {
                                    net_selling_price_aggregate_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_aggregate_unit']);
                                    net_selling_price_basic_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_basic_unit']);
                                    net_selling_price_calculated_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_calculated_unit']);
                                    net_selling_price_commercial_unit.val(($(this).val() / 1.23).toFixed(2));
                                    gross_selling_price_aggregate_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_aggregate_unit']);
                                    gross_selling_price_basic_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_basic_unit']);
                                    gross_selling_price_calculated_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_calculated_unit']);
                                }

                                if ($(this).hasClass('gross_selling_price_basic_unit')) {
                                    net_selling_price_aggregate_unit.val(values['net_selling_price_basic_unit']['net_selling_price_aggregate_unit']);
                                    net_selling_price_commercial_unit.val(values['net_selling_price_basic_unit']['net_selling_price_commercial_unit']);
                                    net_selling_price_calculated_unit.val(values['net_selling_price_basic_unit']['net_selling_price_calculated_unit']);
                                    net_selling_price_basic_unit.val(($(this).val() / 1.23).toFixed(4));
                                    gross_selling_price_aggregate_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_aggregate_unit']);
                                    gross_selling_price_commercial_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_commercial_unit']);
                                    gross_selling_price_calculated_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_calculated_unit']);
                                }

                                if ($(this).hasClass('gross_selling_price_aggregate_unit')) {
                                    net_selling_price_basic_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_basic_unit']);
                                    net_selling_price_commercial_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_commercial_unit']);
                                    net_selling_price_calculated_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_calculated_unit']);
                                    net_selling_price_aggregate_unit.val(($(this).val() / 1.23).toFixed(4));
                                    gross_selling_price_basic_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_basic_unit']);
                                    gross_selling_price_commercial_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_commercial_unit']);
                                    gross_selling_price_calculated_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_calculated_unit']);
                                }

                                if ($(this).hasClass('gross_selling_price_calculated_unit')) {
                                    net_selling_price_basic_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_basic_unit']);
                                    net_selling_price_commercial_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_commercial_unit']);
                                    net_selling_price_aggregate_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_aggregate_unit']);
                                    net_selling_price_calculated_unit.val(($(this).val() / 1.23).toFixed(4));
                                    gross_selling_price_basic_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_basic_unit']);
                                    gross_selling_price_commercial_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_commercial_unit']);
                                    gross_selling_price_aggregate_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_aggregate_unit']);
                                }
                                updateProfit();
                            }
                            $(document).on('change', 'input.price', function() {
                                    console.log('x');
                                    commaReplace('.priceChange');

                                    var itemId = $(this).data('item-id');

                                    var net_purchase_price_commercial_unit = $('.net_purchase_price_commercial_unit[data-item-id="'+itemId+'"');
                                    var net_purchase_price_basic_unit = $('.net_purchase_price_basic_unit[data-item-id="'+itemId+'"');
                                    var net_purchase_price_calculated_unit = $('.net_purchase_price_calculated_unit[data-item-id="'+itemId+'"');
                                    var net_purchase_price_aggregate_unit = $('.net_purchase_price_aggregate_unit[data-item-id="'+itemId+'"');
                                    var net_selling_price_commercial_unit = $('.net_selling_price_commercial_unit[data-item-id="'+itemId+'"');
                                    var net_selling_price_basic_unit = $('.net_selling_price_basic_unit[data-item-id="'+itemId+'"');
                                    var net_selling_price_calculated_unit = $('.net_selling_price_calculated_unit[data-item-id="'+itemId+'"');
                                    var net_selling_price_aggregate_unit = $('.net_selling_price_aggregate_unit[data-item-id="'+itemId+'"');

                                    var gross_purchase_price_commercial_unit = $('.gross_purchase_price_commercial_unit[data-item-id="'+itemId+'"');
                                    var gross_purchase_price_basic_unit = $('.gross_purchase_price_basic_unit[data-item-id="'+itemId+'"');
                                    var gross_purchase_price_calculated_unit = $('.gross_purchase_price_calculated_unit[data-item-id="'+itemId+'"');
                                    var gross_purchase_price_aggregate_unit = $('.gross_purchase_price_aggregate_unit[data-item-id="'+itemId+'"');
                                    var gross_selling_price_commercial_unit = $('.gross_selling_price_commercial_unit[data-item-id="'+itemId+'"');
                                    var gross_selling_price_basic_unit = $('.gross_selling_price_basic_unit[data-item-id="'+itemId+'"');
                                    var gross_selling_price_calculated_unit = $('.gross_selling_price_calculated_unit[data-item-id="'+itemId+'"');
                                    var gross_selling_price_aggregate_unit = $('.gross_selling_price_aggregate_unit[data-item-id="'+itemId+'"');

                                    var numbers_of_basic_commercial_units_in_pack = $('.numbers_of_basic_commercial_units_in_pack[data-item-id="'+itemId+'"');
                                    var number_of_sale_units_in_the_pack = $('.number_of_sale_units_in_the_pack[data-item-id="'+itemId+'"');
                                    var number_of_trade_items_in_the_largest_unit = $('.number_of_trade_items_in_the_largest_unit[data-item-id="'+itemId+'"');
                                    var unit_consumption = $('.unit_consumption[data-item-id="'+itemId+'"');

                                    var net_purchase_price_commercial_unit_value = parseFloat(net_purchase_price_commercial_unit.val());
                                    var net_purchase_price_basic_unit_value = parseFloat(net_purchase_price_basic_unit.val());
                                    var net_purchase_price_calculated_unit_value = parseFloat(net_purchase_price_calculated_unit.val());
                                    var net_purchase_price_aggregate_unit_value = parseFloat(net_purchase_price_aggregate_unit.val());
                                    var net_selling_price_commercial_unit_value = parseFloat(net_selling_price_commercial_unit.val());
                                    var net_selling_price_basic_unit_value = parseFloat(net_selling_price_basic_unit.val());
                                    var net_selling_price_calculated_unit_value = parseFloat(net_selling_price_calculated_unit.val());
                                    var net_selling_price_aggregate_unit_value = parseFloat(net_selling_price_aggregate_unit.val());


                                    var numbers_of_basic_commercial_units_in_pack_value = parseFloat(numbers_of_basic_commercial_units_in_pack.val());
                                    var number_of_sale_units_in_the_pack_value = parseFloat(number_of_sale_units_in_the_pack.val());
                                    var unit_consumption_value = parseFloat(unit_consumption.val());
                                    var values = {
                                        'net_purchase_price_commercial_unit' :
                                            {
                                                'net_purchase_price_aggregate_unit' : (net_purchase_price_commercial_unit_value * number_of_sale_units_in_the_pack_value).toFixed(4),
                                                'net_purchase_price_basic_unit' : (net_purchase_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value).toFixed(4),
                                                'net_purchase_price_calculated_unit' : ((net_purchase_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * unit_consumption_value).toFixed(4),
                                                'gross_purchase_price_aggregate_unit' : ((net_purchase_price_commercial_unit_value * number_of_sale_units_in_the_pack_value) * 1.23).toFixed(4),
                                                'gross_purchase_price_basic_unit' : ((net_purchase_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * 1.23).toFixed(4),
                                                'gross_purchase_price_calculated_unit' : (((net_purchase_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * unit_consumption_value)  * 1.23).toFixed(4),
                                            },
                                        'net_purchase_price_basic_unit' :
                                            {
                                                'net_purchase_price_aggregate_unit' : (net_purchase_price_basic_unit_value * number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value).toFixed(4),
                                                'net_purchase_price_commercial_unit' : (net_purchase_price_basic_unit_value * numbers_of_basic_commercial_units_in_pack_value).toFixed(2),
                                                'net_purchase_price_calculated_unit' : ((net_purchase_price_basic_unit_value * unit_consumption_value)).toFixed(4),
                                                'gross_purchase_price_aggregate_unit' :  ((net_purchase_price_basic_unit_value * number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value * 1.23)).toFixed(4),
                                                'gross_purchase_price_commercial_unit' : ((net_purchase_price_basic_unit_value * numbers_of_basic_commercial_units_in_pack_value)  * 1.23).toFixed(2),
                                                'gross_purchase_price_calculated_unit' :  (((net_purchase_price_basic_unit_value * unit_consumption_value) * 1.23)).toFixed(4)
                                            },
                                        'net_purchase_price_aggregate_unit' :
                                            {
                                                'net_purchase_price_basic_unit' : (net_purchase_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value)).toFixed(4),
                                                'net_purchase_price_commercial_unit' : (net_purchase_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value)).toFixed(2),
                                                'net_purchase_price_calculated_unit' : (net_purchase_price_aggregate_unit_value * (number_of_sale_units_in_the_pack_value)).toFixed(4),
                                                'gross_purchase_price_basic_unit' : ((net_purchase_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value)) * 1.23).toFixed(4),
                                                'gross_purchase_price_commercial_unit' :  ((net_purchase_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(2),
                                                'gross_purchase_price_calculated_unit' : ((net_purchase_price_aggregate_unit_value * (number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(4),
                                            },
                                        'net_purchase_price_calculated_unit' :
                                            {
                                                'net_purchase_price_basic_unit' : ((net_purchase_price_calculated_unit_value / unit_consumption_value)).toFixed(4),
                                                'net_purchase_price_commercial_unit' : ((net_purchase_price_calculated_unit_value / unit_consumption_value) * numbers_of_basic_commercial_units_in_pack_value).toFixed(2),
                                                'net_purchase_price_aggregate_unit' : (((net_purchase_price_calculated_unit_value / number_of_sale_units_in_the_pack_value))).toFixed(4),
                                                'gross_purchase_price_basic_unit' : (((net_purchase_price_calculated_unit_value / unit_consumption_value)) * 1.23 ).toFixed(4),
                                                'gross_purchase_price_commercial_unit' : (((net_purchase_price_calculated_unit_value / unit_consumption_value) * numbers_of_basic_commercial_units_in_pack_value) * 1.23 ).toFixed(2),
                                                'gross_purchase_price_aggregate_unit' : (((net_purchase_price_calculated_unit_value / number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(4),

                                            },
                                        'net_selling_price_commercial_unit' :
                                            {
                                                'net_selling_price_aggregate_unit' : (net_selling_price_commercial_unit_value * number_of_sale_units_in_the_pack_value).toFixed(4),
                                                'net_selling_price_basic_unit' : (net_selling_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value).toFixed(4),
                                                'net_selling_price_calculated_unit' :  ((net_selling_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * unit_consumption_value).toFixed(4),
                                                'gross_selling_price_aggregate_unit' : ((net_selling_price_commercial_unit_value * number_of_sale_units_in_the_pack_value) * 1.23).toFixed(4),
                                                'gross_selling_price_basic_unit' : ((net_selling_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * 1.23).toFixed(4),
                                                'gross_selling_price_calculated_unit' :  (((net_selling_price_commercial_unit_value / numbers_of_basic_commercial_units_in_pack_value) * unit_consumption_value) * 1.23).toFixed(4),
                                            },
                                        'net_selling_price_basic_unit' :
                                            {
                                                'net_selling_price_aggregate_unit' : (net_selling_price_basic_unit_value * number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value).toFixed(2),
                                                'net_selling_price_commercial_unit' : (net_selling_price_basic_unit_value * numbers_of_basic_commercial_units_in_pack_value).toFixed(2),
                                                'net_selling_price_calculated_unit' : ((net_selling_price_basic_unit_value * unit_consumption_value)).toFixed(2),
                                                'gross_selling_price_aggregate_unit' :  ((net_selling_price_basic_unit_value * number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value * 1.23)).toFixed(2),
                                                'gross_selling_price_commercial_unit' : ((net_selling_price_basic_unit_value * numbers_of_basic_commercial_units_in_pack_value)  * 1.23).toFixed(2),
                                                'gross_selling_price_calculated_unit' :  (((net_selling_price_basic_unit_value * unit_consumption_value) * 1.23)).toFixed(2)
                                            },
                                        'net_selling_price_aggregate_unit' :
                                            {
                                                'net_selling_price_basic_unit' : (net_selling_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value)).toFixed(2),
                                                'net_selling_price_commercial_unit' : (net_selling_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value)).toFixed(2),
                                                'net_selling_price_calculated_unit' : (net_selling_price_aggregate_unit_value * (number_of_sale_units_in_the_pack_value)).toFixed(2),
                                                'gross_selling_price_basic_unit' : ((net_selling_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value * numbers_of_basic_commercial_units_in_pack_value)) * 1.23).toFixed(2),
                                                'gross_selling_price_commercial_unit' :  ((net_selling_price_aggregate_unit_value / (number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(2),
                                                'gross_selling_price_calculated_unit' : ((net_selling_price_aggregate_unit_value * (number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(2),
                                            },
                                        'net_selling_price_calculated_unit' :
                                            {
                                                'net_selling_price_basic_unit' : ((net_selling_price_calculated_unit_value / unit_consumption_value)).toFixed(2),
                                                'net_selling_price_commercial_unit' : ((net_selling_price_calculated_unit_value / unit_consumption_value) * numbers_of_basic_commercial_units_in_pack_value).toFixed(2),
                                                'net_selling_price_aggregate_unit' : ((net_selling_price_calculated_unit_value / (number_of_sale_units_in_the_pack_value))).toFixed(2),
                                                'gross_selling_price_basic_unit' : (((net_selling_price_calculated_unit_value / unit_consumption_value)) * 1.23 ).toFixed(2),
                                                'gross_selling_price_commercial_unit' : (((net_selling_price_calculated_unit_value / unit_consumption_value) * numbers_of_basic_commercial_units_in_pack_value) * 1.23 ).toFixed(2),
                                                'gross_selling_price_aggregate_unit' : ((net_selling_price_calculated_unit_value / (number_of_sale_units_in_the_pack_value)) * 1.23).toFixed(2),

                                            }
                                    }



                                    if ($(this).hasClass('net_purchase_price_commercial_unit')) {
                                        net_purchase_price_aggregate_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_aggregate_unit']);
                                        net_purchase_price_basic_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_basic_unit']);
                                        net_purchase_price_calculated_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_calculated_unit']);
                                        gross_purchase_price_commercial_unit.val(($(this).val() * 1.23).toFixed(2));
                                        gross_purchase_price_aggregate_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_aggregate_unit']);
                                        gross_purchase_price_basic_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_basic_unit']);
                                        gross_purchase_price_calculated_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_calculated_unit']);
                                    }

                                    if ($(this).hasClass('net_purchase_price_basic_unit')) {
                                        net_purchase_price_aggregate_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_aggregate_unit']);
                                        net_purchase_price_commercial_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_commercial_unit']);
                                        net_purchase_price_calculated_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_calculated_unit']);
                                        gross_purchase_price_basic_unit.val(($(this).val() * 1.23).toFixed(4));
                                        gross_purchase_price_aggregate_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_aggregate_unit']);
                                        gross_purchase_price_commercial_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_commercial_unit']);
                                        gross_purchase_price_calculated_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_calculated_unit']);
                                    }

                                    if ($(this).hasClass('net_purchase_price_aggregate_unit')) {
                                        net_purchase_price_basic_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_basic_unit']);
                                        net_purchase_price_commercial_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_commercial_unit']);
                                        net_purchase_price_calculated_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_calculated_unit']);
                                        gross_purchase_price_aggregate_unit.val(($(this).val() * 1.23).toFixed(4));
                                        gross_purchase_price_basic_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_basic_unit']);
                                        gross_purchase_price_commercial_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_commercial_unit']);
                                        gross_purchase_price_calculated_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_calculated_unit']);
                                    }

                                    if ($(this).hasClass('net_purchase_price_calculated_unit')) {
                                        net_purchase_price_basic_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_basic_unit']);
                                        net_purchase_price_commercial_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_commercial_unit']);
                                        net_purchase_price_aggregate_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_aggregate_unit']);
                                        gross_purchase_price_calculated_unit.val(($(this).val() * 1.23).toFixed(4));
                                        gross_purchase_price_basic_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_basic_unit']);
                                        gross_purchase_price_commercial_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_commercial_unit']);
                                        gross_purchase_price_aggregate_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_aggregate_unit']);
                                    }

                                    if ($(this).hasClass('gross_purchase_price_commercial_unit')) {
                                        net_purchase_price_aggregate_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_aggregate_unit']);
                                        net_purchase_price_basic_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_basic_unit']);
                                        net_purchase_price_calculated_unit.val(values['net_purchase_price_commercial_unit']['net_purchase_price_calculated_unit']);
                                        net_purchase_price_commercial_unit.val(($(this).val() / 1.23).toFixed(2));
                                        gross_purchase_price_aggregate_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_aggregate_unit']);
                                        gross_purchase_price_basic_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_basic_unit']);
                                        gross_purchase_price_calculated_unit.val(values['net_purchase_price_commercial_unit']['gross_purchase_price_calculated_unit']);
                                    }

                                    if ($(this).hasClass('gross_purchase_price_basic_unit')) {
                                        net_purchase_price_aggregate_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_aggregate_unit']);
                                        net_purchase_price_commercial_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_commercial_unit']);
                                        net_purchase_price_calculated_unit.val(values['net_purchase_price_basic_unit']['net_purchase_price_calculated_unit']);
                                        net_purchase_price_basic_unit.val(($(this).val() / 1.23).toFixed(4));
                                        gross_purchase_price_aggregate_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_aggregate_unit']);
                                        gross_purchase_price_commercial_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_commercial_unit']);
                                        gross_purchase_price_calculated_unit.val(values['net_purchase_price_basic_unit']['gross_purchase_price_calculated_unit']);
                                    }

                                    if ($(this).hasClass('gross_purchase_price_aggregate_unit')) {
                                        net_purchase_price_basic_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_basic_unit']);
                                        net_purchase_price_commercial_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_commercial_unit']);
                                        net_purchase_price_calculated_unit.val(values['net_purchase_price_aggregate_unit']['net_purchase_price_calculated_unit']);
                                        net_purchase_price_aggregate_unit.val(($(this).val() / 1.23).toFixed(4));
                                        gross_purchase_price_basic_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_basic_unit']);
                                        gross_purchase_price_commercial_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_commercial_unit']);
                                        gross_purchase_price_calculated_unit.val(values['net_purchase_price_aggregate_unit']['gross_purchase_price_calculated_unit']);
                                    }

                                    if ($(this).hasClass('gross_purchase_price_calculated_unit')) {
                                        net_purchase_price_basic_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_basic_unit']);
                                        net_purchase_price_commercial_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_commercial_unit']);
                                        net_purchase_price_aggregate_unit.val(values['net_purchase_price_calculated_unit']['net_purchase_price_aggregate_unit']);
                                        net_purchase_price_calculated_unit.val(($(this).val() / 1.23).toFixed(4));
                                        gross_purchase_price_basic_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_basic_unit']);
                                        gross_purchase_price_commercial_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_commercial_unit']);
                                        gross_purchase_price_aggregate_unit.val(values['net_purchase_price_calculated_unit']['gross_purchase_price_aggregate_unit']);
                                    }

                                    if ($(this).hasClass('net_selling_price_commercial_unit')) {
                                        net_selling_price_aggregate_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_aggregate_unit']);
                                        net_selling_price_basic_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_basic_unit']);
                                        net_selling_price_calculated_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_calculated_unit']);
                                        gross_selling_price_commercial_unit.val(($(this).val() * 1.23).toFixed(2));
                                        gross_selling_price_aggregate_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_aggregate_unit']);
                                        gross_selling_price_basic_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_basic_unit']);
                                        gross_selling_price_calculated_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_calculated_unit']);
                                    }

                                    if ($(this).hasClass('net_selling_price_basic_unit')) {
                                        net_selling_price_aggregate_unit.val(values['net_selling_price_basic_unit']['net_selling_price_aggregate_unit']);
                                        net_selling_price_commercial_unit.val(values['net_selling_price_basic_unit']['net_selling_price_commercial_unit']);
                                        net_selling_price_calculated_unit.val(values['net_selling_price_basic_unit']['net_selling_price_calculated_unit']);
                                        gross_selling_price_basic_unit.val(($(this).val() * 1.23).toFixed(4));
                                        gross_selling_price_aggregate_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_aggregate_unit']);
                                        gross_selling_price_commercial_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_commercial_unit']);
                                        gross_selling_price_calculated_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_calculated_unit']);
                                    }

                                    if ($(this).hasClass('net_selling_price_aggregate_unit')) {
                                        net_selling_price_basic_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_basic_unit']);
                                        net_selling_price_commercial_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_commercial_unit']);
                                        net_selling_price_calculated_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_calculated_unit']);
                                        gross_selling_price_aggregate_unit.val(($(this).val() * 1.23).toFixed(4));
                                        gross_selling_price_basic_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_basic_unit']);
                                        gross_selling_price_commercial_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_commercial_unit']);
                                        gross_selling_price_calculated_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_calculated_unit']);
                                    }

                                    if ($(this).hasClass('net_selling_price_calculated_unit')) {
                                        net_selling_price_basic_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_basic_unit']);
                                        net_selling_price_commercial_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_commercial_unit']);
                                        net_selling_price_aggregate_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_aggregate_unit']);
                                        gross_selling_price_calculated_unit.val(($(this).val() * 1.23).toFixed(4));
                                        gross_selling_price_basic_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_basic_unit']);
                                        gross_selling_price_commercial_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_commercial_unit']);
                                        gross_selling_price_aggregate_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_aggregate_unit']);
                                    }

                                    if ($(this).hasClass('gross_selling_price_commercial_unit')) {
                                        net_selling_price_aggregate_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_aggregate_unit']);
                                        net_selling_price_basic_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_basic_unit']);
                                        net_selling_price_calculated_unit.val(values['net_selling_price_commercial_unit']['net_selling_price_calculated_unit']);
                                        net_selling_price_commercial_unit.val(($(this).val() / 1.23).toFixed(2));
                                        gross_selling_price_aggregate_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_aggregate_unit']);
                                        gross_selling_price_basic_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_basic_unit']);
                                        gross_selling_price_calculated_unit.val(values['net_selling_price_commercial_unit']['gross_selling_price_calculated_unit']);
                                    }

                                    if ($(this).hasClass('gross_selling_price_basic_unit')) {
                                        net_selling_price_aggregate_unit.val(values['net_selling_price_basic_unit']['net_selling_price_aggregate_unit']);
                                        net_selling_price_commercial_unit.val(values['net_selling_price_basic_unit']['net_selling_price_commercial_unit']);
                                        net_selling_price_calculated_unit.val(values['net_selling_price_basic_unit']['net_selling_price_calculated_unit']);
                                        net_selling_price_basic_unit.val(($(this).val() / 1.23).toFixed(4));
                                        gross_selling_price_aggregate_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_aggregate_unit']);
                                        gross_selling_price_commercial_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_commercial_unit']);
                                        gross_selling_price_calculated_unit.val(values['net_selling_price_basic_unit']['gross_selling_price_calculated_unit']);
                                    }

                                    if ($(this).hasClass('gross_selling_price_aggregate_unit')) {
                                        net_selling_price_basic_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_basic_unit']);
                                        net_selling_price_commercial_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_commercial_unit']);
                                        net_selling_price_calculated_unit.val(values['net_selling_price_aggregate_unit']['net_selling_price_calculated_unit']);
                                        net_selling_price_aggregate_unit.val(($(this).val() / 1.23).toFixed(4));
                                        gross_selling_price_basic_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_basic_unit']);
                                        gross_selling_price_commercial_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_commercial_unit']);
                                        gross_selling_price_calculated_unit.val(values['net_selling_price_aggregate_unit']['gross_selling_price_calculated_unit']);
                                    }

                                    if ($(this).hasClass('gross_selling_price_calculated_unit')) {
                                        net_selling_price_basic_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_basic_unit']);
                                        net_selling_price_commercial_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_commercial_unit']);
                                        net_selling_price_aggregate_unit.val(values['net_selling_price_calculated_unit']['net_selling_price_aggregate_unit']);
                                        net_selling_price_calculated_unit.val(($(this).val() / 1.23).toFixed(4));
                                        gross_selling_price_basic_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_basic_unit']);
                                        gross_selling_price_commercial_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_commercial_unit']);
                                        gross_selling_price_aggregate_unit.val(values['net_selling_price_calculated_unit']['gross_selling_price_aggregate_unit']);
                                    }
                                    updateProfit();
                                }

                            );



                            function update(purchase, selling, id) {
                                var purchasePrice = $("input[name='"+purchase+"["+id+"]']").val();
                                var sellingPrice = $("input[name='"+selling+"["+id+"]']");
                                var vat = 1.23;
                                var calc;

                                calc = parseFloat(purchasePrice) * parseFloat(vat);

                                sellingPrice.val(parseFloat(calc).toFixed(4));

                            }

                            function update2(purchase, selling, id) {
                                var purchasePrice = $("input[name='"+purchase+"["+id+"]']").val();
                                var sellingPrice = $("input[name='"+selling+"["+id+"]']");
                                var vat = 1.23;
                                var calc;

                                calc = parseFloat(purchasePrice) * parseFloat(vat);

                                sellingPrice.val(parseFloat(calc).toFixed(2));

                            }
                            @foreach ($order->items as $item)
                            update2('net_purchase_price_commercial_unit','gross_purchase_price_commercial_unit','{{$item->id}}');
                            update('net_purchase_price_basic_unit','gross_purchase_price_basic_unit','{{$item->id}}');
                            update('net_purchase_price_calculated_unit','gross_purchase_price_calculated_unit','{{$item->id}}');
                            update('net_purchase_price_aggregate_unit','gross_purchase_price_aggregate_unit','{{$item->id}}');
                            update2('net_selling_price_commercial_unit','gross_selling_price_commercial_unit','{{$item->id}}');
                            update('net_selling_price_basic_unit','gross_selling_price_basic_unit','{{$item->id}}');
                            update('net_selling_price_calculated_unit','gross_selling_price_calculated_unit','{{$item->id}}');
                            update('net_selling_price_aggregate_unit','gross_selling_price_aggregate_unit','{{$item->id}}');
                            @endforeach

                            $(function()
                            {
                                $( "#add-item" ).autocomplete({
                                    source : "{{ route('orders.products.autocomplete') }}",
                                    minLength: 1,
                                    select: function(event, ui) {
                                        $('#add-item').val(ui.item.value);
                                    }
                                });
                            });
                            $('#add-item').on('change', function(){
                                $.ajax({
                                    type: 'GET',
                                    url: '/admin/orders/products/'+this.value,
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function(data) {
                                        var currentId = $('.id').length;
                                        var id = parseInt(currentId)+1;

                                        $('#products-tbody:last-child').append(
                                            '<tr class="id" id="id['+ id +']">\n' +
                                            '<td><img src="' + replaceImageUrl(data.product.url) + '" class="product-image">' + '<h4><strong>'+id+'. </strong> '+data.product.name+' </h4></td>\n' +
                                            '<input name="id['+id+']"\n' +
                                            'value="'+id+'" type="hidden"\n' +
                                            'class="form-control" id="id['+id+']">' +
                                            '<input name="product_id['+id+']"\n' +
                                            'value="'+data.product.id+'" type="hidden"\n' +
                                            'class="form-control" id="product_id['+id+']">' +
                                            '<input value="1" type="hidden"\n' +
                                            'class="form-control item_quantity"  name="item_quantity['+id+']" data-item-id="'+id+'">\n' +
                                            '<input name="numbers_of_basic_commercial_units_in_pack['+id+']"\n' +
                                            'data-item-id="'+id+'" value="'+data.packing.numbers_of_basic_commercial_units_in_pack+'" type="hidden"\n' +
                                            'class="form-control numbers_of_basic_commercial_units_in_pack" id="numbers_of_basic_commercial_units_in_pack['+id+']">\n' +
                                            '<input name="number_of_sale_units_in_the_pack['+id+']"\n' +
                                            'data-item-id="'+id+'" value="'+data.packing.number_of_sale_units_in_the_pack+'" type="hidden"\n' +
                                            'class="form-control number_of_sale_units_in_the_pack" id="number_of_sale_units_in_the_pack['+id+']">\n' +
                                            '<input name="number_of_trade_items_in_the_largest_unit['+id+']"\n' +
                                            'data-item-id="'+id+'" value="'+data.packing.number_of_trade_items_in_the_largest_unit+'" type="hidden"\n' +
                                            'class="form-control number_of_trade_items_in_the_largest_unit" id="number_of_trade_items_in_the_largest_unit['+id+']">\n' +
                                            '<input name="unit_consumption['+id+']"\n' +
                                            'data-item-id="'+id+'" value="'+data.packing.unit_consumption+'" type="hidden"\n' +
                                            'class="form-control unit_consumption" id="unit_consumption['+id+']">'+
                                            '</tr>' +
                                            '<tr>\n' +
                                            '<th>Cena zakupu</th>\n' +
                                            '</tr><tr class="purchase-row">\n' +
                                            '<td>\n' +
                                            '<input name="net_purchase_price_commercial_unit['+id+']" data-item-id="'+id+'" value="'+parseFloat(data.price.net_purchase_price_commercial_unit).toFixed(2)+'" type="text" class="form-control price net_purchase_price_commercial_unit" id="net_purchase_price_commercial_unit['+id+']">\n' +
                                            '\n' +
                                            '<input name="net_purchase_price_basic_unit['+id+']" data-item-id="'+id+'" value="'+parseFloat(data.price.net_purchase_price_basic_unit).toFixed(2)+'" type="text" class="form-control price net_purchase_price_basic_unit" id="net_purchase_price_basic_unit['+id+']">\n' +
                                            '\n' +
                                            '<input name="net_purchase_price_calculated_unit['+id+']" data-item-id="'+id+'" value="'+parseFloat(data.price.net_purchase_price_calculated_unit).toFixed(2)+'" type="text" class="form-control price net_purchase_price_calculated_unit" id="net_purchase_price_calculated_unit['+id+']">\n' +
                                            '\n' +
                                            '<input name="net_purchase_price_aggregate_unit['+id+']" data-item-id="'+id+'" value="'+parseFloat(data.price.net_purchase_price_aggregate_unit).toFixed(2)+'" type="text" class="form-control price net_purchase_price_aggregate_unit" id="net_purchase_price_aggregate_unit['+id+']">\n' +
                                            '</td>\n' +
                                            '</tr><tr class="purchase-row">\n' +
                                            '<td>\n' +
                                            '<input name="gross_purchase_price_commercial_unit['+id+']" value="'+parseFloat(data.price.net_purchase_price_commercial_unit * 1.23).toFixed(2)+'" type="text" data-item-id="'+id+'" class="form-control price gross_purchase_price_commercial_unit" id="gross_purchase_price_commercial_unit['+id+']">\n' +
                                            '\n' +
                                            '<input name="gross_purchase_price_basic_unit['+id+']" value="'+parseFloat(data.price.net_purchase_price_basic_unit * 1.23).toFixed(2)+'" type="text" data-item-id="'+id+'" class="form-control price gross_purchase_price_basic_unit" id="gross_purchase_price_basic_unit['+id+']">\n' +
                                            '\n' +
                                            '<input name="gross_purchase_price_calculated_unit['+id+']" value="'+parseFloat(data.price.net_purchase_price_calculated_unit* 1.23).toFixed(2) +'" type="text" data-item-id="'+id+'" class="form-control price gross_purchase_price_calculated_unit" id="gross_purchase_price_calculated_unit['+id+']">\n' +
                                            '\n' +
                                            '<input name="gross_purchase_price_aggregate_unit['+id+']" value="'+parseFloat(data.price.net_purchase_price_aggregate_unit * 1.23).toFixed(2)+'" type="text" data-item-id="'+id+'" class="form-control price gross_purchase_price_aggregate_unit" id="gross_purchase_price_aggregate_unit['+id+']">\n' +
                                            '</td>\n' +
                                            '</tr><tr class="purchase-row">\n' +
                                            '<td>\n' +
                                            '<input name="unit_commercial" value="1 '+data.packing.unit_commercial+'" type="text" class="form-control" id="unit_commercial" disabled="">\n' +
                                            '\n' +
                                            '<input name="unit_basic" value="'+ parseFloat(data.packing.numbers_of_basic_commercial_units_in_pack)+' '+ data.packing.unit_basic+'" type="text" class="form-control" id="unit_basic" disabled="">\n' +
                                            '\n' +
                                            '<input name="calculation_unit" value="'+ parseFloat(data.packing.numbers_of_basic_commercial_units_in_pack / data.packing.unit_consumption).toFixed(2)+' '+data.packing.calculation_unit+'" type="text" class="form-control" id="calculation_unit" disabled="">\n' +
                                            '\n' +
                                            '<input name="unit_of_collective" value="'+data.packing.number_of_sale_units_in_the_pack+' '+data.packing.unit_of_collective+'" type="text" class="form-control" id="unit_of_collective" disabled="">\n' +
                                            '</td>\n' +
                                            '</tr><tr>\n' +
                                            '<th>Cena sprzedaży</th>\n' +
                                            '</tr><tr class="selling-row">\n' +
                                            '<td>\n' +
                                            '<input name="net_selling_price_commercial_unit['+id+']" data-item-id="'+id+'" value="'+parseFloat(data.price.net_selling_price_commercial_unit).toFixed(2)+'" type="text" class="form-control price net_selling_price_commercial_unit change-order" id="net_selling_price_commercial_unit['+id+']">\n' +
                                            '\n' +
                                            '<input name="net_selling_price_basic_unit['+id+']" data-item-id="'+id+'" value="'+parseFloat(data.price.net_selling_price_basic_unit).toFixed(2)+'" type="text" class="form-control price net_selling_price_basic_unit change-order" id="net_selling_price_basic_unit['+id+']">\n' +
                                            '\n' +
                                            '<input name="net_selling_price_calculated_unit['+id+']" data-item-id="'+id+'" value="'+parseFloat(data.price.net_selling_price_calculated_unit).toFixed(2)+'" type="text" class="form-control price net_selling_price_calculated_unit change-order" id="net_selling_price_calculated_unit['+id+']">\n' +
                                            '\n' +
                                            '<input name="net_selling_price_aggregate_unit['+id+']" data-item-id="'+id+'" value="'+parseFloat(data.price.net_selling_price_aggregate_unit).toFixed(2)+'" type="text" class="form-control price net_selling_price_aggregate_unit change-order" id="net_selling_price_aggregate_unit['+id+']">\n' +
                                            '</td>\n' +
                                            '</tr><tr class="selling-row">\n' +
                                            '<td>\n' +
                                            '<input name="gross_selling_price_commercial_unit['+id+']" data-item-id="'+id+'" value="'+parseFloat(data.price.net_selling_price_commercial_unit * 1.23).toFixed(2)+'" type="text" class="form-control price gross_selling_price_commercial_unit change-order" id="gross_selling_price_commercial_unit['+id+']">\n' +
                                            '\n' +
                                            '<input name="gross_selling_price_basic_unit['+id+']" data-item-id="'+id+'" value="'+parseFloat(data.price.net_selling_price_basic_unit * 1.23).toFixed(2)+'" type="text" class="form-control price gross_selling_price_basic_unit change-order" id="gross_selling_price_basic_unit['+id+']">\n' +
                                            '\n' +
                                            '<input name="gross_selling_price_calculated_unit['+id+']" data-item-id="'+id+'" value="'+parseFloat(data.price.net_selling_price_calculated_unit * 1.23).toFixed(2)+'" type="text" class="form-control price gross_selling_price_calculated_unit change-order" id="gross_selling_price_calculated_unit['+id+']">\n' +
                                            '\n' +
                                            '<input name="gross_selling_price_aggregate_unit['+id+']" data-item-id="'+id+'" value="'+parseFloat(data.price.net_selling_price_calculated_unit * 1.23).toFixed(2)+'" type="text" class="form-control price gross_selling_price_aggregate_unit change-order" id="gross_selling_price_aggregate_unit['+id+']">\n' +
                                            '</td>\n' +
                                            '</tr>' +
                                            '<tr class="purchase-row">\n' +
                                            '<td>\n' +
                                            '<input name="unit_commercial" value="1 '+data.packing.unit_commercial+'" type="text" class="form-control" id="unit_commercial" disabled="">\n' +
                                            '\n' +
                                            '<input name="unit_basic" value="'+ parseFloat(data.packing.numbers_of_basic_commercial_units_in_pack)+' '+ data.packing.unit_basic+'" type="text" class="form-control" id="unit_basic" disabled="">\n' +
                                            '<input name="calculation_unit" value="'+ parseFloat(data.packing.numbers_of_basic_commercial_units_in_pack / data.packing.unit_consumption).toFixed(2)+' '+data.packing.calculation_unit+'" type="text" class="form-control" id="calculation_unit" disabled="">\n' +
                                            '<input name="unit_of_collective" value="'+data.packing.number_of_sale_units_in_the_pack+' '+data.packing.unit_of_collective+'" type="text" class="form-control" id="unit_of_collective" disabled="">\n' +
                                            '</td>\n' +
                                            '</tr><tr>\n' +
                                            '<th>Ilość</th>\n' +
                                            '</tr><tr class="selling-row">\n' +
                                            '<td>\n' +
                                            '<input name="quantity_commercial['+id+']" value="1" data-item-id="'+id+'" type="text" class="form-control price" id="quantity_commercial['+id+']">\n' +
                                            '</td>\n' +
                                            '</tr><tr>\n' +
                                            '<th>Zysk</th>\n' +
                                            '</tr><tr class="selling-row">\n' +
                                            '<td>\n' +
                                            '<input type="text" class="form-control item-profit" data-item-id="'+id+'" disabled="" name="item-profit" value="0.00">\n' +
                                            '</td>\n' +
                                            '</tr>'
                                        );
                                        $('#add-item').val(' ');

                                    }
                                });
                            });
                            var sh = 0;
                            $('input.change-order').on('change', function(){
                                //   $('#new-order').show();
                                sh = 1;
                            });

                            $('[name="change-button-form"]').on('click', function () {
                                value = this.value;
                                $('#' + value).show();
                                if (value === 'general' && sh === 1) {
                                    $('#new-order').show();
                                }
                            });

                            //  $('#new-order').hide();
                        });

                        function commaReplace(cssClass) {
                            document.querySelectorAll(cssClass).forEach(function(input) {
                                input.value = input.value.replace(/,/g, '.');
                            });
                        }

                        function replaceImageUrl(url) {
                            let validUrl = url.replace('C\:\\z\\', 'https://mega1000.pl/z/');
                        }

                        $( document ).ready(function() {
                            commaReplace('.priceChange');
                            let valueOfItemsGross;
                            let packingWarehouseCost;
                            let shipmentPriceForClient;
                            let additionalServiceCost;

                            if($('#additional_service_cost').val() == '') {
                                additionalServiceCost = 0;
                            } else {
                                additionalServiceCost = parseFloat($('#additional_service_cost').val());
                            }


                            let profit = parseFloat($('#profit').val().replace(',', ''));
                            $("#profitInfo").val(profit);
                            $("#totalPriceInfo").val($("#orderItemsSum").val());
                            $("#weightInfo").val($("#weight").val());
                            updateOrderSum();

                            $('.sumChange').on('change', function() {
                                updateOrderSum();
                            });


                        });
                        function updateOrderSum(profit = null) {
                            if($('#totalPriceInfo').val() == '') {
                                valueOfItemsGross = 0;
                            } else {
                                valueOfItemsGross = parseFloat($('#orderItemsSum').val().replace(',', ''));
                            }
                            if($('#additional_cash_on_delivery_cost').val() == '') {
                                packingWarehouseCost = 0;
                            } else {
                                packingWarehouseCost = parseFloat($('#additional_cash_on_delivery_cost').val());
                            }

                            if($('#shipment_price_for_client').val() == '') {
                                shipmentPriceForClient = 0;
                            } else {
                                shipmentPriceForClient = parseFloat($('#shipment_price_for_client').val());
                            }

                            if($('#additional_service_cost').val() == '') {
                                additionalServieCost = 0;
                            } else {
                                additionalServieCost = parseFloat($('#additional_service_cost').val());
                            }

                            if(profit == 1) {
                                $('#profitInfo').val((parseFloat($('#profit').val()) + additionalServieCost).toFixed(2));
                            }
			    //let sum = $('#totalPriceInfo').val() + packingWarehouseCost + shipmentPriceForClient + additionalServieCost;
                            let sum = valueOfItemsGross + packingWarehouseCost + shipmentPriceForClient + additionalServieCost;
			    console.log('Gross' + valueOfItemsGross);
			    console.log('Suma' + sum);
                            $('#orderValueSum').val(sum.toFixed(2));
                            $('#left_to_pay_on_delivery').val((sum - parseFloat($('#payments').val())).toFixed(2));
                        }
                        $('#status').on('change', function(){
                            $('#shouldBeSent').attr('checked', true);
                        });
                        $( document ).ready(function() {
                            let newOrder = 1;

                            $('#newSplitOrder').on('click', function() {
                                showSplitButtons('.btn-split');
                                switch(newOrder) {
                                    case 1:
                                        showSubsplitOrder('.firstOrder');
                                        $('[name="firstOrderExist"]').val(1);
                                        break;
                                    case 2:
                                        showSubsplitOrder('.secondOrder');
                                        $('[name="secondOrderExist"]').val(1);
                                        break;
                                    case 3:
                                        showSubsplitOrder('.thirdOrder');
                                        $('[name="thirdOrderExist"]').val(1);
                                        break;
                                    case 4:
                                        showSubsplitOrder('.fourthOrder');
                                        $('[name="fourthOrderExist"]').val(1);
                                        break;
                                    case 5:
                                        showSubsplitOrder('.fifthOrder');
                                        $('[name="fifthOrderExist"]').val(1);
                                        break;
                                    default:

                                }
                                newOrder++;
                            });

                            $('.splitQuantity').on('change', function() {
                                let itemId = $(this).data('item-id');
                                let name = '[name="modal_quantity_commercial[' + itemId + ']"]';
                                let base = $(this).next().find('[name="base[' + itemId + ']"]');
                                let productQuantity = $(name);
                                let productQuantityValue = productQuantity.val();
                                productQuantity.val(productQuantity.val() - $(this).val());
                                let leftClass = '[name="left[' + $(this).data('item-id') + ']"]';
                                let quantityLeft = base.text() - sumSplitOrdersQuantity(itemId);
                                let type = $(this).data('order-type');
                                changeWeight(type, itemId, $(this).val());
                                changeLeft(leftClass, quantityLeft);
                            });


                            $('.dkoInput').on('change', function() {
                                changeDko();
                            });

                            $('.dkpInput').on('change', function() {
                                changeDkp();
                            });

                            $('.shipmentClient').on('change', function() {
                                changeShipmentClient();
                            });

                            $('.shipmentUs').on('change', function() {
                                changeShipmentUs();
                            });

                            $('.splitDko').on('click', function() {
                                let orderDko = {{ $order->additional_service_cost ?? 0 }};
                                splitCosts('additional_service_cost', orderDko, 'DKO');
                            });

                            $('.splitDkp').on('click', function() {
                                let orderDkp = {{ $order->additional_cash_on_delivery_cost ?? 0 }};
                                splitCosts('additional_cash_on_delivery_cost', orderDkp, 'DKP');
                            });

                            $('.splitClient').on('click', function() {
                                let shipmentClient = {{ $order->shipment_price_for_client ?? 0 }};
                                splitCosts('shipment_price_for_client', shipmentClient, 'SHIP_CLIENT');
                            });

                            $('.splitUs').on('click', function() {
                                let shipmentUs = {{ $order->shipment_price_for_us ?? 0 }};
                                splitCosts('shipment_price_for_us', shipmentUs, 'SHIP_US');
                            });

                        });
                        function changeDko(split = null)
                        {
                            let dkoDifference;
                            let orderDko = {{ $order->additional_service_cost ?? 0 }};
                            if(split != null) {
                                dkoDifference = parseFloat(orderDko - split);
                            } else {
                                dkoDifference = parseFloat(orderDko - sumSplitOrdersDko());
                            }

                            changeLeft('.dkoLeft', dkoDifference);
                        }

                        function changeDkp(split = null)
                        {
                            let dkpDifference;
                            let orderDkp = {{ $order->additional_cash_on_delivery_cost ?? 0 }};
                            if(split != null) {
                                dkpDifference = parseFloat(orderDkp - split);
                            } else {
                                dkpDifference = parseFloat(orderDkp - sumSplitOrdersDkp());
                            }
                            changeLeft('.dkpLeft', dkpDifference);
                        }

                        function changeShipmentClient(split = null)
                        {
                            let orderClientShipment = {{ $order->shipment_price_for_client ?? 0 }};
                            let orderClientShipmentDifference;
                            if(split != null) {
                                orderClientShipmentDifference = parseFloat(orderClientShipment - split);
                            } else {
                                orderClientShipmentDifference = parseFloat(orderClientShipment - sumSplitOrdersShipmentClient());
                            }
                            changeLeft('.shipmentClientLeft', orderClientShipmentDifference);
                        }

                        function changeShipmentUs(split = null)
                        {
                            let orderShipmentUs = {{ $order->shipment_price_for_us ?? 0 }};
                            let orderShipmentUsDifference;
                            if(split != null) {
                                orderShipmentUsDifference = parseFloat(orderShipmentUs - split);
                            } else {
                                orderShipmentUsDifference = parseFloat(orderShipmentUs - sumSplitOrdersShipmentUs());
                            }
                            changeLeft('.shipmentUsLeft', orderShipmentUsDifference);
                        }

                        function sumSplitOrdersQuantity(id)
                        {
                            let sumSplitOrdersQuantity = ~~parseInt($('[name="firstOrderQuantity[' + id + ']"]').val()) + ~~parseInt($('[name="secondOrderQuantity[' + id + ']"]').val()) + ~~parseInt($('[name="thirdOrderQuantity[' + id + ']"]').val()) + ~~parseInt($('[name="fourthOrderQuantity[' + id + ']"]').val()) + ~~parseInt($('[name="fifthOrderQuantity[' + id + ']"]').val());

                            return sumSplitOrdersQuantity;
                        }

                        function sumSplitOrdersDko(id)
                        {
                            let sumSplitOrdersDko = ~~parseFloat($('[name="additional_service_cost_firstOrder"]').val()) + ~~parseFloat($('[name="additional_service_cost_secondOrder"]').val()) + ~~parseFloat($('[name="additional_service_cost_thirdOrder"]').val()) + ~~parseFloat($('[name="additional_service_cost_fourthOrder"]').val()) + ~~parseFloat($('[name="additional_service_cost_fifthOrder"]').val());
                            return sumSplitOrdersDko;
                        }

                        function sumSplitOrdersDkp(id)
                        {
                            let sumSplitOrdersDkp = ~~parseFloat($('[name="additional_cash_on_delivery_cost_firstOrder"]').val()) + ~~parseFloat($('[name="additional_cash_on_delivery_cost_secondOrder"]').val()) + ~~parseFloat($('[name="additional_cash_on_delivery_cost_thirdOrder"]').val()) + ~~parseFloat($('[name="additional_cash_on_delivery_cost_fourthOrder"]').val()) + ~~parseFloat($('[name="additional_cash_on_delivery_cost_fifthOrder"]').val());

                            return sumSplitOrdersDkp;
                        }

                        function sumSplitOrdersShipmentClient(id)
                        {
                            let sumSplitOrdersShipmentClient = ~~parseFloat($('[name="shipment_price_for_client_firstOrder"]').val()) + ~~parseFloat($('[name="shipment_price_for_client_secondOrder"]').val()) + ~~parseFloat($('[name="shipment_price_for_client_thirdOrder"]').val()) + ~~parseFloat($('[name="shipment_price_for_client_fourthOrder"]').val()) + ~~parseFloat($('[name="shipment_price_for_client_fifthOrder"]').val());

                            return sumSplitOrdersShipmentClient;
                        }

                        function sumSplitOrdersShipmentUs(id)
                        {
                            let sumSplitOrdersShipmentUs = ~~parseFloat($('[name="shipment_price_for_us_firstOrder"]').val()) + ~~parseFloat($('[name="shipment_price_for_us_secondOrder"]').val()) + ~~parseFloat($('[name="shipment_price_for_us_thirdOrder"]').val()) + ~~parseFloat($('[name="shipment_price_for_us_fourthOrder"]').val()) + ~~parseFloat($('[name="shipment_price_for_us_fifthOrder"]').val());

                            return sumSplitOrdersShipmentUs;
                        }

                        function splitCosts(elementsName, value, type)
                        {
                            let func;
                            switch(type) {
                                case 'DKO':
                                    func = changeDko(value);
                                    break;
                                case 'DKP':
                                    func = changeDkp(value);
                                    break;
                                case 'SHIP_CLIENT':
                                    func = changeShipmentClient(value);
                                    break;
                                case 'SHIP_US':
                                    func = changeShipmentUs(value);
                                    break;
                                default:
                            }
                            if($('[name="firstOrderExist"]').val() == 1 && $('[name="secondOrderExist"]').val() == 0) {
                                $('[name="' + elementsName + '_firstOrder"]').val(value);
                                func;
                            } else if($('[name="secondOrderExist"]').val() == 1 && $('[name="secondOrderExist"]').val() == 1 && $('[name="thirdOrderExist"]').val() == 0) {
                                value = (value/2).toFixed(2);
                                $('[name="' + elementsName +'_firstOrder"]').val(value);
                                $('[name="' + elementsName + '_secondOrder"]').val(value);
                                func;
                            } else if($('[name="secondOrderExist"]').val() == 1 && $('[name="secondOrderExist"]').val() == 1 && $('[name="thirdOrderExist"]').val() == 1 && $('[name="fourthOrderExist"]').val() == 0 ){
                                value = (value/3).toFixed(2);
                                $('[name="' + elementsName + '_firstOrder"]').val(value);
                                $('[name="' + elementsName + '_secondOrder"]').val(value);
                                $('[name="' + elementsName + '_thirdOrder"]').val(value);
                                func;
                            } else if($('[name="secondOrderExist"]').val() == 1 && $('[name="secondOrderExist"]').val() == 1 && $('[name="thirdOrderExist"]').val() == 1 && $('[name="fourthOrderExist"]').val() == 1 && $('[name="fourthOrderExist"]').val() == 0 ){
                                value = (value/4).toFixed(2);
                                $('[name="' + elementsName + '_firstOrder"]').val(value);
                                $('[name="' + elementsName + '_secondOrder"]').val(value);
                                $('[name="' + elementsName + '_thirdOrder"]').val(value);
                                $('[name="' + elementsName + '_fourthOrder"]').val(value);
                                func;
                            } else {
                                value = (value/5).toFixed(2);
                                $('[name="' + elementsName + '_firstOrder"]').val(value);
                                $('[name="' + elementsName + '_secondOrder"]').val(value);
                                $('[name="' + elementsName + '_thirdOrder"]').val(value);
                                $('[name="' + elementsName + '_fourthOrder"]').val(value);
                                $('[name="' + elementsName + '_fifthOrder"]').val(value);
                                func;
                            }
                        }
                        function changeLeft(elementsClass, value) {
                            $( elementsClass ).each(function() {
                                $( this ).text( value );
                            });
                        }

                        function showSubsplitOrder(elementsClass) {
                            $( elementsClass ).each(function() {
                                $( this ).css('display', 'table-cell');
                            });
                        }

                        function showSplitButtons(elementsClass) {
                            $( elementsClass ).each(function() {
                                $( this ).css('display', 'block');
                            });
                        }

                        function getFirmData(id) {
                            var symbol = $('#firms_data').val();
                            window.location.href = '/admin/orders/' + id + '/getDataFromFirm/'+ symbol;
                        }

                        function changeWeight(type, id, quantity) {
                            let weightSelector = 'input[name="' + type + 'OrderWeightValue[' + id + ']"]';
                            let weightValue = $(weightSelector).val();
                            let weight = parseInt(weightValue * quantity);
                            let weightValueSelector = '[name="' + type + 'OrderWeight[' + id + ']"]';
                            if(weight == 0) {
                                $(weightValueSelector).text(weightValue + ' kg');
                            } else {
                                $(weightValueSelector).text(weight + ' kg');
                            }

                            let weightBase = '[name="' + type + 'OrderWeightValueBase[' + id + ']"]';
                            $(weightBase).val(weight);

                            let weightSum = 0;
                            $('.' + type + 'WeightValueBase').each(function(item){
                                weightSum += parseFloat($('.' + type + 'WeightValueBase')[item].value);
                            });

                            let globalWeightTextSelector = '.' + type + 'OrderWeightSum';
                            $(globalWeightTextSelector).text('Waga: ' + weightSum + ' kg');
                        }

                        function fillQuantity(id, quantity, type) {
                            let selector = '[name="' + type + 'OrderQuantity[' + id + ']"]';
                            $(selector).val(quantity);
                            $(selector).trigger('change');
                        }

                        $('.openPaymentModal').on('click', function(){
                            let masterPaymentId = $(this).data('payment');
                            let masterPaymentAmount = $(this).data('payment-amount');
                            $('#paymentModal input[name="masterPaymentAmount"]').val(masterPaymentAmount);
                            $('input[name="masterPaymentId"]').val(masterPaymentId);
                            $('#paymentModal').modal();
                        });

                        $('.openPromiseModal').on('click', function(){
                            let masterPaymentId = $(this).data('payment');
                            let masterPaymentAmount = $(this).data('payment-amount');
                            $('input[name="masterPaymentId"]').val(masterPaymentId);
                            $('input[name="amount"]').val(masterPaymentAmount);
                            $('#promiseModal').modal();
                        });

                        $('#chooseOrder').on('change', function() {
                            let orderId = $(this).val();
                            let orderValue = parseFloat($('input[name="order-payment-' + orderId + '"]').val());
                            let masterPaymentAmount = parseFloat($('#paymentModal input[name="masterPaymentAmount"]').val());
                            if(masterPaymentAmount > orderValue) {
                                $('#orderPayment #amount').val(orderValue);
                            } else if(masterPaymentAmount < orderValue) {
                                $('#orderPayment #amount').val(masterPaymentAmount);
                            }

                        });
                    </script>

     <script src="{{ URL::asset('js/views/orders/edit.js') }}"></script>
                        <style>
                            .firstOrder, .secondOrder, .thirdOrder, .fourthOrder, .fifthOrder {
                                display: none;
                            }
                        </style>
@endsection
