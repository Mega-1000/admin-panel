@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stocks.title')
    </h1>
@endsection

@section('table')
    <a href="{{ route('sets.index') }}" class="btn btn-success">@lang('sets.packet_list')</a>
    <a href="{{ route('product_stocks.print') }}" class="btn btn-success">Wydrukuj stany</a>
    <div class="vue-components">
        <tracker :enabled="true" :user="{{ Auth::user()->id }}"/>
    </div>
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th>@lang('voyager.generic.actions')</th>
            <th>@lang('product_stocks.table.name')</th>
            <th>@lang('product_stocks.table.symbol')</th>
            <th>@lang('product_stocks.table.url')</th>
            <th>@lang('product_stocks.table.manufacturer')</th>
            <th>@lang('product_stocks.table.positions')</th>
            <th>@lang('product_stocks.table.quantity')</th>
            <th>@lang('product_stocks.table.min_quantity')</th>
            <th>@lang('product_stocks.table.unit')</th>
            <th>@lang('product_stocks.table.start_quantity')</th>
            <th>@lang('product_stocks.table.number_on_a_layer')</th>
            <th>@lang('product_stocks.table.net_purchase_price_commercial_unit')</th>
            <th>@lang('product_stocks.table.net_purchase_price_commercial_unit_after_discounts')</th>
            <th>@lang('product_stocks.table.net_special_price_commercial_unit')</th>
            <th>@lang('product_stocks.table.net_purchase_price_basic_unit')</th>
            <th>@lang('product_stocks.table.net_purchase_price_basic_unit_after_discounts')</th>
            <th>@lang('product_stocks.table.net_special_price_basic_unit')</th>
            <th>@lang('product_stocks.table.net_purchase_price_calculated_unit')</th>
            <th>@lang('product_stocks.table.net_purchase_price_calculated_unit_after_discounts')</th>
            <th>@lang('product_stocks.table.net_special_price_calculated_unit')</th>
            <th>@lang('product_stocks.table.gross_purchase_price_aggregate_unit')</th>
            <th>@lang('product_stocks.table.gross_purchase_price_aggregate_unit_after_discounts')</th>
            <th>@lang('product_stocks.table.gross_special_price_aggregate_unit')</th>
            <th>@lang('product_stocks.table.gross_purchase_price_the_largest_unit')</th>
            <th>@lang('product_stocks.table.gross_purchase_price_the_largest_unit_after_discounts')</th>
            <th>@lang('product_stocks.table.gross_special_price_the_largest_unit')</th>
            <th>@lang('product_stocks.table.net_selling_price_commercial_unit')</th>
            <th>@lang('product_stocks.table.net_selling_price_basic_unit')</th>
            <th>@lang('product_stocks.table.net_selling_price_calculated_unit')</th>
            <th>@lang('product_stocks.table.net_selling_price_aggregate_unit')</th>
            <th>@lang('product_stocks.table.net_selling_price_the_largest_unit')</th>
            <th>@lang('product_stocks.table.discount1')</th>
            <th>@lang('product_stocks.table.discount2')</th>
            <th>@lang('product_stocks.table.discount3')</th>
            <th>@lang('product_stocks.table.bonus1')</th>
            <th>@lang('product_stocks.table.bonus2')</th>
            <th>@lang('product_stocks.table.bonus3')</th>
            <th>@lang('product_stocks.table.gross_price_of_packing')</th>
            <th>@lang('product_stocks.table.table_price')</th>
            <th>@lang('product_stocks.table.vat')</th>
            <th>@lang('product_stocks.table.additional_payment_for_milling')</th>
            <th>@lang('product_stocks.table.coating')</th>
            <th>@lang('product_stocks.table.created_at')</th>
        </tr>
        </thead>
    </table>
@endsection


@section('datatable-scripts')
    <script>
        const deleteRecord = (id) => {
            $('#delete_form')[0].action = "{{ url()->current() }}/" + id;
            $('#delete_modal').modal('show');
        };

        var breadcrumb = $('.breadcrumb:nth-child(2)');

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='javascript:void();'>Stany magazynowe</a></li>");

        // DataTable
        let table = $('#dataTable').DataTable({
            language: '{!! json_encode( __('voyager.datatable'), true) !!}',
            processing: true,
            serverSide: true,
            "lengthMenu": [[10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500, "Wszystkie"]],
            columnDefs: [
                {className: "dt-center", targets: "_all"}
            ],
            dom: 'Bfrtip',
            buttons: [
                {extend: 'colvis', text: 'Widzialność kolumn'}
            ],
            order: [[0, "asc"]],
            ajax: {
                url: '{!! route('product_stocks.datatable') !!}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            },
            language: {
                buttons: {
                    pageLength: {
                        _: "Pokaż %d zamówień",
                        '-1': "Wszystkie"
                    }
                }
            },
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
                    data: 'id',
                    name: 'id',
                    render: function (id) {
                        let html = '<a href="{{ url()->current() }}/' + id + '/edit" class="btn btn-sm btn-primary edit">';
                        html += '<i class="voyager-edit"></i>';
                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>';
                        html += '</a>';
                        return html;
                    }
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'symbol',
                    name: 'symbol'
                },
                {
                    data: 'url',
                    name: 'url'
                },
                {
                    data: 'manufacturer',
                    name: 'manufacturer'
                },
                {
                    data: 'id',
                    name: 'positions',
                    render: function (data, type, row) {
                        let html = '';
                        if (row.positions) {
                            row.positions.forEach(function (position) {
                                html += 'Alejka: ' + position.lane + ' Regał: ' + position.bookstand + ' Półka: ' + position.shelf + ' Pozycja: ' + position.position + ' Ilość: ' + position.position_quantity;
                                html += '<br/>';
                                html += 'JZ:';
                                if(row.number_of_sale_units_in_the_pack != 0)
                                    html += Math.floor(position.position_quantity / row.number_of_sale_units_in_the_pack);
                                else {
                                    html += 0;
                                }
                                html += '<br/> JH:';
                                if(row.number_of_sale_units_in_the_pack == 0 || position.position_quantity < 0) {
                                    html += position.position_quantity;
                                } else {
                                    html += position.position_quantity - (Math.floor(position.position_quantity / row.number_of_sale_units_in_the_pack) * row.number_of_sale_units_in_the_pack);
                                }
                                html += '<br/><br/>';
                            });
                        }
                        return html;

                    }
                },
                {
                    data: 'id',
                    name: 'quantity',
                    render: function(data, type, row) {
                                let html = 0;
                                if (row.positions) {
                                    row.positions.forEach(function (position) {
                                        html += parseInt(position.position_quantity);
                                    });

                                }

                                if(html < row.min_quantity) {
                                    html = '<span style="color: red; font-weight: bold;">' + html + '</span>';
                                }

                                return html;

                    },
                    searchable: false,
                },
                {
                    data: 'min_quantity',
                    name: 'min_quantity'
                },
                {
                    data: 'unit',
                    name: 'unit'
                },
                {
                    data: 'start_quantity',
                    name: 'start_quantity'
                },
                {
                    data: 'number_on_a_layer',
                    name: 'number_on_a_layer'
                },
                {
                    data: 'net_purchase_price_commercial_unit',
                    name: 'net_purchase_price_commercial_unit',
                    defaultContent: ''
                },
                {
                    data: 'net_purchase_price_commercial_unit_after_discounts',
                    name: 'net_purchase_price_commercial_unit_after_discounts',
                    defaultContent: ''
                },
                {
                    data: 'net_special_price_commercial_unit',
                    name: 'net_special_price_commercial_unit',
                    defaultContent: ''
                },
                {
                    data: 'net_purchase_price_basic_unit',
                    name: 'net_purchase_price_basic_unit',
                    defaultContent: ''
                },
                {
                    data: 'net_purchase_price_basic_unit_after_discounts',
                    name: 'net_purchase_price_basic_unit_after_discounts',
                    defaultContent: ''
                },
                {
                    data: 'net_special_price_basic_unit',
                    name: 'net_special_price_basic_unit',
                    defaultContent: ''
                },
                {
                    data: 'net_purchase_price_calculated_unit',
                    name: 'net_purchase_price_calculated_unit',
                    defaultContent: ''
                },
                {
                    data: 'net_purchase_price_calculated_unit_after_discounts',
                    name: 'net_purchase_price_calculated_unit_after_discounts',
                    defaultContent: ''
                },
                {
                    data: 'net_special_price_calculated_unit',
                    name: 'net_special_price_calculated_unit',
                    defaultContent: ''
                },
                {
                    data: 'gross_purchase_price_aggregate_unit',
                    name: 'gross_purchase_price_aggregate_unit',
                    defaultContent: ''
                },
                {
                    data: 'gross_purchase_price_aggregate_unit_after_discounts',
                    name: 'gross_purchase_price_aggregate_unit_after_discounts',
                    defaultContent: ''
                },
                {
                    data: 'gross_special_price_aggregate_unit',
                    name: 'gross_special_price_aggregate_unit',
                    defaultContent: ''
                },
                {
                    data: 'gross_purchase_price_the_largest_unit',
                    name: 'gross_purchase_price_the_largest_unit',
                    defaultContent: ''
                },
                {
                    data: 'gross_purchase_price_the_largest_unit_after_discounts',
                    name: 'gross_purchase_price_the_largest_unit_after_discounts',
                    defaultContent: ''
                },
                {
                    data: 'gross_special_price_the_largest_unit',
                    name: 'gross_special_price_the_largest_unit',
                    defaultContent: ''
                },
                {
                    data: 'net_selling_price_commercial_unit',
                    name: 'net_selling_price_commercial_unit',
                    defaultContent: ''
                },
                {
                    data: 'net_selling_price_basic_unit',
                    name: 'net_selling_price_basic_unit',
                    defaultContent: ''
                },
                {
                    data: 'net_selling_price_calculated_unit',
                    name: 'net_selling_price_calculated_unit',
                    defaultContent: ''
                },
                {
                    data: 'net_selling_price_aggregate_unit',
                    name: 'net_selling_price_aggregate_unit',
                    defaultContent: ''
                },
                {
                    data: 'net_selling_price_the_largest_unit',
                    name: 'net_selling_price_the_largest_unit',
                    defaultContent: ''
                },
                {
                    data: 'discount1',
                    name: 'discount1'
                },
                {
                    data: 'discount2',
                    name: 'discount2'
                },
                {
                    data: 'discount3',
                    name: 'discount3'
                },
                {
                    data: 'bonus1',
                    name: 'bonus1'
                },
                {
                    data: 'bonus2',
                    name: 'bonus2'
                },
                {
                    data: 'bonus3',
                    name: 'bonus3'
                },
                {
                    data: 'gross_price_of_packing',
                    name: 'gross_price_of_packing'
                },
                {
                    data: 'table_price',
                    name: 'table_price'
                },
                {
                    data: 'vat',
                    name: 'vat'
                },
                {
                    data: 'additional_payment_for_milling',
                    name: 'additional_payment_for_milling'
                },
                {
                    data: 'coating',
                    name: 'coating'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                }
            ]
        });
                @foreach($visibilities as $key =>$row)

        var {{'show'.$row->name}}  = @json($row->show);
        {{'show'.$row->name}} = {{'show'.$row->name}}.map(function (x) {
            // if (typeof table.column(x+':name').index() === "number")
            return table.column(x + ':name').index();
        });
        {{'show'.$row->name}} = {{'show'.$row->name}}.filter(function (el) {
            return el != null;
        });

        var {{'hidden'.$row->name}} = @json($row->hidden);
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.map(function (x) {
            // if (typeof table.column(x+':name').index() === "number")
            return table.column(x + ':name').index();
        });
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.filter(function (el) {
            return el != null;
        });
        table.button().add({{1+$key}}, {
            extend: 'colvisGroup',
            text: '{{$row->display_name}}',
            show: {{'show'.$row->name}},
            hide: {{'hidden'.$row->name}}
        });
        @endforeach

        $('#dataTable thead tr th').each(function (i) {
            var title = $(this).text();
            if (title == 'Pozycje produktu') {

            }
            else if (title == 'Stan magazynowy') {
                $(this).html('<div><span>' + title + '</span></div><div class="input_div"><select name="" id="filterQuantity"><option value="all">Wszystkie</option><option value="on_stock">Na stanie</option><option value="without-dash">Bez kresek</option></select></div>');
            } else if (title !== '' && title !== 'Akcje') {
                $(this).html('<div><span>' + title + '</span></div><div><input type="text" placeholder="Szukaj ' + title + '" id="columnSearch' + i + '"/></div>');
            } else if (title == 'Akcje') {
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

            $("#filterQuantity", this).click(function (e) {
                e.stopPropagation();
            });
            $("#filterQuantity", this).change(function () {
                console.log(this.value);
                if (table.column(i).search() !== this.value) {
                    if (this.value == 'all') {
                        table
                            .column(i)
                            .search('')
                            .draw();
                    } else {
                        table
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                }
            });

        });


        $('#dataTable').on('column-visibility.dt', function (e, settings, column, state) {
            console.log(column);
            if (state == true) {
                $("#columnSearch" + column).parent().show();
            } else {
                $("#columnSearch" + column).parent().hide();
            }

        });
    </script>
@endsection
