@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('warehouse_orders.edit')
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
    <form action="{{ action('WarehouseOrdersController@update', ['id' => $warehouseOrder->id]) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('put') }}
                <div class="warehouse-orders-general" id="general">
            <div class="form-group">
                <label for="name">@lang('warehouse_orders.form.created_at')</label>
                <input type="text" class="form-control" id="created_at" name="created_at"
                       value="{{ $warehouseOrder->created_at }}" disabled>
            </div>
            <div class="form-group">
               <h2>Obecny status zamówienia: {{ $warehouseOrder->status ?? '' }}</h2>
            </div>
            <div class="form-group">
                <label for="status">@lang('warehouse_orders.form.status')</label>
                <select class="form-control text-uppercase" name="status">
                    <option value="NEW">@lang('warehouse_orders.form.new')</option>
                    <option value="SENT">@lang('warehouse_orders.form.sent')</option>
                    <option value="ACCEPTED">@lang('warehouse_orders.form.accepted')</option>
                    <option value="CLOSED">@lang('warehouse_orders.form.closed')</option>
                </select>
            </div>
            <div class="form-group">
                <label for="company">@lang('warehouse_orders.form.company')</label>
                <input type="text" class="form-control" id="company" name="company"
                       value="{{ $warehouseOrder->company ?? '' }}">
            </div>
            <div class="form-group">
                <label for="warehouse">@lang('warehouse_orders.form.warehouse')</label>
                <input type="text" class="form-control" id="warehouse" name="warehouse"
                       value="{{ $warehouseOrder->company ?? '' }}">
            </div>
            <div class="form-group">
                <label for="warehouse_mail">@lang('warehouse_orders.form.warehouse_mail')</label>
                <input type="text" class="form-control" id="warehouse_mail" name="warehouse_mail"
                       value="{{ $warehouseOrder->email ?? '' }}">
                <button class="btn btn-success" name="sendEmail" type="button" data-id="{{ $warehouseOrder->id }}">Wyślij email</button>
            </div>
            <div class="form-group">
                <label for="shipment_date">@lang('warehouse_orders.form.planned_shipment_date')</label>
                <input type="date" class="form-control" id="shipment_date" name="shipment_date"
                       value="{{ \Carbon\Carbon::parse($warehouseOrder->shipment_date)->format('Y-m-d') ?? '' }}">
            </div>
            <div class="form-group">
                <label for="comments_for_warehouse">@lang('warehouse_orders.form.comments_for_warehouse')</label>
                <textarea rows="5" cols="50" class="form-control" id="comments_for_warehouse"
                          name="comments_for_warehouse"
                >{{ $warehouseOrder->comments_for_warehouse ?? '' }}</textarea>
            </div>
            <div class="form-group">
                <label for="comments_for_warehouse">@lang('warehouse_orders.form.warehouse_comments')</label>
                <textarea rows="5" cols="50" class="form-control" id="warehouse_comments"
                          name="warehouse_comments"
                >{{ $warehouseOrder->warehouse_comments ?? '' }}</textarea>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nazwa</th>
                    <th scope="col">Ilość</th>
                    <th scope="col">Cena za opakowanie</th>
                    <th scope="col">Cena elementów</th>
                    <th scope="col">Akcje</th>
                </tr>
                </thead>
                <tbody>
                @foreach($warehouseOrder->items as $item)
                <tr>
                    <td><img src="{!! str_replace('C:\\z\\', env('APP_URL') . 'storage/', $item->product->url) !!}" style="width: 179px; height: 130px;"></td>
                    <td>{{ $item->product->name }}</td>
                    <td><input type="text" name="itemQuantity[{{$item->id}}]" value="{{ $item->quantity }}" class="form-control itemQuantity" data-id="{{$item->id}}"></td>
                    <td><input type="text" name="itemPrice[{{$item->id}}]" data-id="{{$item->id}}" class="form-control itemPrice" value="{{ $item->product->price->net_purchase_price_commercial_unit }}"></td>
                    <td><input type="number" name="itemValue[{{$item->id}}]" data-id="{{$item->id}}" class="form-control" value="{{ $item->product->price->net_purchase_price_commercial_unit * $item->quantity }}" disabled></td>
                    <td>Delete</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@section('scripts')
    <script>
        $('.itemQuantity').on('change', function() {
            console.log($(this).data('id'));
           $('[name="itemValue[' + $(this).data('id') + ']"]').val(($('[name="itemPrice[' + $(this).data('id') + ']"]').val() * $('[name="itemQuantity[' + $(this).data('id') + ']"]').val()).toFixed(2));
        });

        $('.itemPrice').on('change', function() {
            console.log($(this).data('id'));
            $('[name="itemValue[' + $(this).data('id') + ']"]').val(($('[name="itemPrice[' + $(this).data('id') + ']"]').val() * $('[name="itemQuantity[' + $(this).data('id') + ']"]').val()).toFixed(2));
        });

        $('[name="sendEmail"]').on('click', function() {
            $.ajax({
                type: "POST",
                url: '{!! route('warehouse.orders.sendEmail') !!}',
                data: {
                    data: {
                        'id': $(this).data('id'),
                        'email': $('[name="warehouse_mail"]').val(),
                    }
                },
            }).done(function(data) {
                alert('Pomyślnie wysłano email');
            });
        });
    </script>
    <script>
        // var breadcrumb = $('.breadcrumb:nth-child(2)');
        //
        // breadcrumb.children().remove();
        // breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        // breadcrumb.append("<li class='active'><a href='/admin/statuses/'>Statusy</a></li>");
        // breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
    </script>
@endsection
