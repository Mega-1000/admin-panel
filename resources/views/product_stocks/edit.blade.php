@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stocks.edit')
        <a style="margin-left: 15px;" href="{{ action('ProductStocksController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('product_stocks.list')</span>
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
    <div style="margin-bottom: 15px;" class="tab">
        <button class="btn btn-primary active"
                name="change-button-form" id="button-general"
                value="general">@lang('product_stocks.form.buttons.general')</button>
        <button class="btn btn-primary"
                name="change-button-form" id="button-stocks"
                value="stocks">@lang('product_stocks.form.buttons.stocks')</button>
        <button class="btn btn-primary"
                name="change-button-form" id="button-positions"
                value="positions">@lang('product_stocks.form.buttons.positions')</button>
        <button class="btn btn-primary"
                name="change-button-form" id="button-logs"
                value="logs">@lang('product_stocks.form.buttons.logs')</button>
        <a id="create-button-position" style="float:right;margin-right: 15px;"
           href="{{route('product_stocks.position.create', ['id' => $productStocks->id]) }}"
           class="btn btn-success install pull-right">
            <i class="voyager-plus"></i> <span>@lang('product_stocks.form.buttons.position_create')</span>
        </a>
        <h4 class="inline">Produkt: {{ $productStocks->product->name }}</h4> - <h4 class="inline">Symbol: {{ $productStocks->product->symbol }}</h4>
    </div>
    <form action="{{ action('ProductStocksController@update', ['id' => $productStocks->id]) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="product_stocks-general" id="general">
            <div class="form-group">
                <label for="name">@lang('product_stocks.form.name')</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="{{ $productStocks->product->name }}" disabled>
            </div>
            <div class="form-group">
                <label for="symbol">@lang('product_stocks.form.symbol')</label>
                <input type="text" class="form-control" id="symbol" name="symbol"
                       value="{{ $productStocks->product->symbol }}" disabled>
            </div>
            <div class="form-group">
                <label for="url">@lang('product_stocks.form.url')</label>
                <input type="text" class="form-control" id="url" name="url"
                       value="{{ $productStocks->product->url }}" disabled>
            </div>
            <div class="form-group">
                <label for="manufacturer">@lang('product_stocks.form.manufacturer')</label>
                <input type="text" class="form-control" id="manufacturer" name="manufacturer"
                       value="{{ $productStocks->product->manufacturer }}" disabled>
            </div>
            @if(count($similarProducts) > 0 && $productStocks->product->stock_product !== true)
                <div class="form-group">
                    <label for="manufacturer">@lang('product_stocks.form.found_similar_products')</label>
                    @foreach($similarProducts as $similarProduct)
                      <h4>@lang('product_stocks.form.symbol'): {{ $similarProduct->symbol }}</h4>
                    @endforeach
                    <label for="stock_product">@lang('product_stocks.form.set_product_as_stock_product')</label>
                    <input type="checkbox" id="stock_product" name="stock_product">
                </div>
            @else
                <div class="form-group">
                    <label for="manufacturer">@lang('product_stocks.form.found_similar_products')</label>
                    @foreach($similarProducts as $similarProduct)
                        <h4>@lang('product_stocks.form.symbol'): {{ $similarProduct->symbol }}</h4>
                    @endforeach
                    <label for="stock_product">@lang('product_stocks.form.product_is_stock_product')</label>
                    <input type="checkbox" id="stock_product" name="stock_product" checked>
                </div>
            @endif
        </div>
        <div class="product_stocks-stocks" id="stocks">
            <div class="form-group">
                <label for="quantity">@lang('product_stocks.form.quantity')</label>
                @php
                    $quantity = 0;
                @endphp
                @foreach ($productStocks->position as $position)
                    @php
                        $quantity += $position->position_quantity
                    @endphp
                @endforeach
                <input type="number" class="form-control" id="quantity" name="quantity"
                       value="{{ $productStocks->quantity }}" disabled>
                <input type="hidden" class="form-control" id="different" name="different"
                       value="">
            </div>
            <div class="form-group">
                <label for="min_quantity">@lang('product_stocks.form.min_quantity')</label>
                <input type="number" class="form-control" id="min_quantity" name="min_quantity"
                       value="{{ $productStocks->min_quantity }}">
            </div>
            <div class="form-group">
                <label for="unit">@lang('product_stocks.form.unit')</label>
                <input type="text" class="form-control" id="unit" name="unit"
                       value="{{ $productStocks->unit }}">
            </div>
            <div class="form-group">
                <label for="start_quantity">@lang('product_stocks.form.start_quantity')</label>
                <input type="number" class="form-control" id="start_quantity" name="start_quantity"
                       value="{{ $productStocks->start_quantity }}">
            </div>
            <div class="form-group">
                <label for="number_on_a_layer">@lang('product_stocks.form.number_on_a_layer')</label>
                <input type="number" class="form-control" id="number_on_a_layer" name="number_on_a_layer"
                       value="{{ $productStocks->number_on_a_layer }}">
            </div>
        </div>
        <div class="product_stocks-positions" id="positions">
            <table style="width: 100%" id="dataTablePositions" class="table table-hover">
                <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>@lang('product_stock_positions.table.lane')</th>
                    <th>@lang('product_stock_positions.table.bookstand')</th>
                    <th>@lang('product_stock_positions.table.shelf')</th>
                    <th>@lang('product_stock_positions.table.position')</th>
                    <th>@lang('product_stock_positions.table.position_quantity')</th>
                    <th>ilość uszkodzonych</th>
                    <th>@lang('warehouses.table.created_at')</th>
                    <th>@lang('voyager.generic.actions')</th>
                </tr>
                </thead>
            </table>
        </div>
        <div class="product_stocks-logs" id="logs">
            <table style="width: 100%" id="dataTableLogs" class="table table-hover">
                <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>@lang('product_stock_logs.table.product_stock_id')</th>
                    <th>@lang('product_stock_logs.table.product_stock_position_id')</th>
                    <th>@lang('product_stock_logs.table.action')</th>
                    <th>@lang('product_stock_logs.table.quantity')</th>
                    <th>@lang('product_stock_logs.table.quantity_after_action')</th>
                    <th>@lang('product_stock_logs.table.order_id')</th>
                    <th>@lang('product_stock_logs.table.username')</th>
                    <th>@lang('product_stock_logs.table.firstname')</th>
                    <th>@lang('product_stock_logs.table.lastname')</th>
                    <th>@lang('warehouses.table.created_at')</th>
                    <th>@lang('voyager.generic.actions')</th>
                </tr>
                </thead>
            </table>
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
    <div class="vue-components">
        <tracker :enabled="true" :user="{{ Auth::user()->id }}"/>
    </div>
@endsection
@section('datatable-scripts')
    <script>
        $(document).ready(function () {
            var general = $('#general').show();
            var stocks = $('#stocks').hide();
            var positions = $('#positions').hide();
            var logs = $('#logs').hide();
            var pageTitle = $('.page-title').children('i');
            var createButtonPosition = $('#create-button-position').hide();
            var value;
            var referrer = document.referrer;
            var breadcrumb = $('.breadcrumb');
            var item = '{{old('tab')}}';
            var getUrlParameter = function getUrlParameter(sParam) {
                var sPageURL = window.location.search.substring(1),
                    sURLVariables = sPageURL.split('&'),
                    sParameterName,
                    i;

                for (i = 0; i < sURLVariables.length; i++) {
                    sParameterName = sURLVariables[i].split('=');

                    if (sParameterName[0] === sParam) {
                        return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                    }
                }
            };
            var tabParam = getUrlParameter('tab');

            breadcrumb.children().remove();
            breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
            breadcrumb.append("<li class='active'><a href='/admin/products/stocks'>Stany magazynowe</a></li>");
            breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
            if (referrer.search('positions') != -1 || tabParam === 'positions' || item === 'positions') {
                $('#button-general').removeClass('active');
                $('#button-stocks').removeClass('active');
                $('#button-positions').addClass('active');
                $('#button-logs').removeClass('active');
                general.hide();
                stocks.hide();
                positions.show();
                logs.hide();
                createButtonPosition.show();
                pageTitle.removeClass();
                pageTitle.addClass('voyager-eye');
                breadcrumb.children().remove();
                breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
                breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$productStocks->id}}/edit'>Stany magazynowe</a></li>");
                breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
                breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$productStocks->id}}/edit#positions'>Pozycje magazynowe</a></li>");
            } else if (referrer.search('logs') != -1 || tabParam === 'logs' || item === 'logs') {
                $('#button-general').removeClass('active');
                $('#button-stocks').removeClass('active');
                $('#button-positions').removeClass('active');
                $('#button-logs').addClass('active');
                general.hide();
                stocks.hide();
                positions.hide();
                logs.show();
                createButtonPosition.hide();
                pageTitle.removeClass();
                pageTitle.addClass('voyager-logbook');
                breadcrumb.children().remove();
                breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
                breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$productStocks->id}}/edit'>Stany magazynowe</a></li>");
                breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
                breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$productStocks->id}}/edit#logs'>Historia zmian</a></li>");

            }
            $('[name="change-button-form"]').on('click', function () {
                value = this.value;
                $('#' + value).show();
                if (value === 'general') {
                    $('#button-general').addClass('active');
                    $('#button-stocks').removeClass('active');
                    $('#button-positions').removeClass('active');
                    $('#button-logs').removeClass('active');
                    general.show();
                    stocks.hide();
                    positions.hide();
                    logs.hide();
                    createButtonPosition.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-book');
                    breadcrumb.children().remove();
                    breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
                    breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$productStocks->id}}/edit'>Stany magazynowe</a></li>");
                    breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
                } else if (value === 'stocks') {
                    $('#button-general').removeClass('active');
                    $('#button-stocks').addClass('active');
                    $('#button-positions').removeClass('active');
                    $('#button-logs').removeClass('active');
                    general.hide();
                    stocks.show();
                    positions.hide();
                    logs.hide();
                    createButtonPosition.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-bar-chart');
                    breadcrumb.children().remove();
                    breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
                    breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$productStocks->id}}/edit'>Stany magazynowe</a></li>");
                    breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
                } else if (value === 'positions') {
                    $('#button-general').removeClass('active');
                    $('#button-stocks').removeClass('active');
                    $('#button-positions').addClass('active');
                    $('#button-logs').removeClass('active');
                    general.hide();
                    stocks.hide();
                    positions.show();
                    logs.hide();
                    createButtonPosition.show();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-eye');
                    breadcrumb.children().remove();
                    breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
                    breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$productStocks->id}}/edit'>Stany magazynowe</a></li>");
                    breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
                    breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$productStocks->id}}/edit#positions'>Pozycje magazynowe</a></li>");
                } else if (value === 'logs') {
                    $('#button-general').removeClass('active');
                    $('#button-stocks').removeClass('active');
                    $('#button-positions').removeClass('active');
                    $('#button-logs').addClass('active');
                    general.hide();
                    stocks.hide();
                    positions.hide();
                    logs.show();
                    createButtonPosition.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-logbook');
                    breadcrumb.children().remove();
                    breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
                    breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$productStocks->id}}/edit'>Stany magazynowe</a></li>");
                    breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
                    breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$productStocks->id}}/edit#logs'>Historia zmian</a></li>");
                }
            });

        });

    </script>
    <script>
        const deleteRecordPositions = (id) =>{
            $('#delete_form')[0].action = "/admin/products/stocks/{{$productStocks->id}}/positions/" + id;
            $('#delete_modal').modal('show');
        };
        $.fn.dataTable.ext.errMode = 'throw';
        // DataTable
        let tablePositions = $('#dataTablePositions').DataTable({
            language: {!! json_encode( __('voyager.datatable'), true) !!},
            processing: true,
            serverSide: true,
            columnDefs: [
                {className: "dt-center", targets: "_all"}
            ],
            order: [[0, "asc"]],
            ajax: '{!! route('product_stocks.position.datatable', ['id' => $productStocks->id]) !!}',
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
                    data: 'lane',
                    name: 'lane'
                },
                {
                    data: 'bookstand',
                    name: 'bookstand'
                },
                {
                    data: 'shelf',
                    name: 'shelf'
                },
                {
                    data: 'position',
                    name: 'position'
                },
                {
                    data: 'position_quantity',
                    name: 'position_quantity'
                },
                {
                    data: 'damaged',
                    name: 'damaged'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function (id, type, row) {
                        let html = '<a href="/admin/products/stocks/{{$id}}/positions/' + id + '/edit" class="btn btn-sm btn-primary edit">';
                        html += '<i class="voyager-edit"></i>';
                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>';
                        html += '</a>';
                        html += '<a href="#" onclick="deleteRecordPositions('+id+')" class="btn btn-sm btn-danger delete delete-record">';
                        html += '<i class="voyager-trash"></i>';
                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>';
                        html += '</a>';
                        html += '<button type="button" data-quantity="' + row.position_quantity + '" id="moveButton-' + row.id + '" class="btn btn-sm btn-warning edit" onclick="moveData(' + row.id + ')">Przenieś towar stąd</button>';
                        html += '<button type="button" id="moveButtonAjax-' + row.id + '" class="btn btn-sm btn-success btn-move edit hidden" onclick="moveDataAjax(' + row.id + ')">Przenieś towar tutaj</button>';
                        return html;
                    }
                }
            ]
        });
        function moveData(id) {
            if ($('#moveButton-' + id).hasClass('btn-warning')) {
                $('#moveButton-' + id).removeClass('btn-warning').addClass('btn-dark');
                $('.btn-warning').attr('disabled', true);
                $('.btn-move').removeClass('hidden');
            } else if ($('#moveButton-' + id).hasClass('btn-dark')) {
                $('#moveButton-' + id).removeClass('btn-dark').addClass('btn-warning');
                $('.btn-warning').attr('disabled', false);
                $('.btn-move').addClass('hidden');
            }
        }

        function moveDataAjax(id) {
            let idToSend = id;
            let buttonId = $('.btn-dark').attr('id');
            let idToGet;
            let res = buttonId.split("-")
            idToGet = res[1];
            let quantity = $('#moveButton-' + idToGet).data('quantity');
            if (idToGet != idToSend) {
                $('#quantity__move').val(quantity);
                $('#order_id_get').text(idToGet);
                $('#order_id_send').text(idToSend);
                $('#move_position_quantity').modal('show');
            } else {
                $('#move_position_quantity_error').modal('show');
            }
        }

        $('#move_position_quantity_ok').on('click', function () {
            var idToGet = $('#order_id_get').text();
            var idToSend = $('#order_id_send').text();
            $.ajax({
                type: 'POST',
                data: {'quantity__move' : $('#quantity__move').val()},
                url: '/admin/positions/' + idToGet + '/' + idToSend + '/quantity' + '/move'
            }).done(function (data) {
                $('#order_move_data_success').modal('show');
                let currentUrl = window.location.href;
                window.location.href = currentUrl + '?tab=positions';
            }).fail(function () {
                $('#order_move_data_error').modal('show');
                $('#order_move_data_ok_error').on('click', function () {
                    window.location.href = '/admin/orders';
                });
            });
        });

        @foreach($visibilitiesPosition as $key =>$row)

        var {{'show'.$row->name}}  = @json($row->show);
        {{'show'.$row->name}} = {{'show'.$row->name}}.map(function(x){
            // if (typeof table.column(x+':name').index() === "number")
            return tablePositions.column(x+':name').index();
        });
        {{'show'.$row->name}} = {{'show'.$row->name}}.filter(function (el) {
            return el != null;
        });

        var {{'hidden'.$row->name}} = @json($row->hidden);
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.map(function(x){
            // if (typeof table.column(x+':name').index() === "number")
            return tablePositions.column(x+':name').index();
        });
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.filter(function (el) {
            return el != null;
        });
        tablePositions.button().add({{1+$key}},{
            extend: 'colvisGroup',
            text: '{{$row->display_name}}',
            show: {{'show'.$row->name}},
            hide: {{'hidden'.$row->name}}
        });
        @endforeach

        $('#dataTablePositions thead tr th').each(function (i) {
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

        var quantity = $('#quantity').val();
        var added;
        $('#quantity').on('change', function(){
           var quantityNow = $('#quantity').val();
           var different;
            quantity = parseInt(quantity);
            quantityNow = parseInt(quantityNow);
           if($('#stocks > div:nth-child(1) > label > span') !== undefined){
               $('#stocks > div:nth-child(1) > label > span').remove();
           }
           if(quantity > quantityNow) {
               different = parseFloat(quantity) - parseFloat(quantityNow);
               $('#stocks > div:nth-child(1) > label').append('<span style="color:red"> -'+ different +'</span>');
               $('#different').val('-' + different);
           } else if(quantity < quantityNow){
               different = quantityNow - quantity;
               $('#stocks > div:nth-child(1) > label').append('<span style="color:green"> +'+ different +'</span>');
               $('#different').val('+' + different);
           } else if(quantityNow === quantity){
               different = 0;
               $('#stocks > div:nth-child(1) > label > span').remove();
               $('#different').val(different);
           }

            if(different !== 0){
                if(added !== 1) {
                    $('#stocks > div:nth-child(1)').append(
                        ' <div class="form-group" style="margin-top:15px">\n' +
                        '                <label for="select_position">@lang('product_stocks.form.select_position')</label>\n' +
                        '                <select class="form-control text-uppercase" name="select_position">\n' +
                            @foreach($productStocks->position as $position)
                                '                    <option value="{{$position->id}}">ID: {{$position->id}} Aleja: {{$position->lane}} Regał: {{$position->bookstand}} Półka: {{$position->shelf}} Pozycja {{$position->position}} Ilość w tym miejscu: {{$position->position_quantity}}</option>' +
                            @endforeach
                                '                </select>\n' +
                        '            </div>'
                    );
                    added = 1;
                }
           }
        });
    </script>
    <script>
        $.fn.dataTable.ext.errMode = 'throw';
        // DataTable
        let tableLogs = $('#dataTableLogs').DataTable({
            language: {!! json_encode( __('voyager.datatable'), true) !!},
            processing: true,
            serverSide: true,
            columnDefs: [
                {className: "dt-center", targets: "_all"}
            ],
            order: [[0, "desc"]],
            ajax: '{!! route('product_stocks.logs.datatable', ['id' => $productStocks->id]) !!}',
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
                    data: 'product_stock_id',
                    name: 'product_stock_id'
                },
                {
                    data: 'product_stock_position_id',
                    name: 'product_stock_position_id'
                },
                {
                    data: 'action',
                    name: 'action',
                    render: function(action) {
                        if (action === 'ADD') {
                            return '<span style="color: green;">' + {!! json_encode(__('product_stock_logs.table.add'), true) !!} + '</span>';
                        } else {
                            return '<span style="color: red;">' + {!! json_encode(__('product_stock_logs.table.delete'), true) !!} + '</span>';
                        }
                    }
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'stock_quantity_after_action',
                    name: 'stock_quantity_after_action'
                },
                {
                    data: 'order_id',
                    name: 'order_id'
                },
                {
                    data: 'user.name',
                    name: 'user.name'
                },
                {
                    data: 'user.firstname',
                    name: 'user.firstname'
                },
                {
                    data: 'user.lastname',
                    name: 'user.lastname'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function (id) {
                        let html = '<a href="/admin/products/stocks/{{$productStocks->id}}/logs/' + id + '/show" class="btn btn-sm btn-primary edit">';
                        html += '<i class="voyager-show"></i>';
                        html += '<span class="hidden-xs hidden-sm"> @lang('product_stock_logs.show')</span>';
                        html += '</a>';
                        return html;
                    }
                }
            ]
        });
        @foreach($visibilitiesLogs as $key =>$row)

        var {{'show'.$row->name}}  = @json($row->show);
        {{'show'.$row->name}} = {{'show'.$row->name}}.map(function(x){
            // if (typeof table.column(x+':name').index() === "number")
            return tableLogs.column(x+':name').index();
        });
        {{'show'.$row->name}} = {{'show'.$row->name}}.filter(function (el) {
            return el != null;
        });

        var {{'hidden'.$row->name}} = @json($row->hidden);
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.map(function(x){
            // if (typeof table.column(x+':name').index() === "number")
            return tableLogs.column(x+':name').index();
        });
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.filter(function (el) {
            return el != null;
        });
        tableLogs.button().add({{1+$key}},{
            extend: 'colvisGroup',
            text: '{{$row->display_name}}',
            show: {{'show'.$row->name}},
            hide: {{'hidden'.$row->name}}
        });
        @endforeach


        $('#dataTableLogs thead tr th').each(function (i) {
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

    </script>
@endsection
