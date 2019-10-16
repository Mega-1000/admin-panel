@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-window-list"></i> @lang('warehouse_orders.title')
    </h1>
    <style>
	.itemQuantity {
		width: 100px;
	}
	.btn {
		padding: 0px;
	}

    #dataTable-warehouseOrder {
        table-layout: fixed;
    }

	table.dataTable tbody td {
		padding: 0px;
	}

    .dt-center {
        max-width: 300px !important;
    }

    /*#dataTable-warehouseOrder thead input {
        max-width: 50px !important;
    }

    #dataTable-warehouseOrder thead div {
        max-width: 50px !important;
    }

    #dataTable-warehouseOrder thead tr th {
        max-width: 130px !important;
    }*/

	.dt-center span {
		word-break: break-all:
	}
        .pointer {
            cursor: pointer;
        }
        .ui-tooltip {
            width: 400px !important;
        }
	#dataTable-warehouseOrder #columnSearch32, #columnSearch33, #columnSearch36, #columnSearch37, #columnSearch40, #columnSearch41, #columnSearch45, #columnSearch46, #columnSearch50, #columnSearch51 {
    width: 50px;
}
    </style>
@endsection

@section('table')
    <div style="display: flex; align-items: center;" id="add-label-container">
        <button class="btn btn-warning" onclick="clearFilters()">Wyszczyść filtry</button>
    </div>
    <h4>Wartość zamówienia: <span id="orderValue" style="color: red;"></span></h4>
    <h4>Waga zamówienia: <span id="orderWeight" style="color: red;"></span></h4>
    <table id="dataTable-warehouseOrder" class="table table-hover spacious-container">
        <thead>
        <tr>
            <th></th>
            <th colspan="2">Mega1000</th>
            <th colspan="6">@lang('warehouse_orders.table.manufacturer')</th>
            <th colspan="22">@lang('warehouse_orders.table.quantities')</th>
            <th colspan="7">@lang('warehouse_orders.table.commercial_units')</th>
            <th colspan="4">@lang('warehouse_orders.table.collective_units')</th>
            <th colspan="13">@lang('warehouse_orders.table.transport_units')</th>
        </tr>
        <tr>
            <th>@lang('warehouse_orders.table.id')</th>
            <th>@lang('warehouse_orders.table.symbol')</th>
            <th>@lang('warehouse_orders.table.name')</th>
            <th>@lang('warehouse_orders.table.manufacturer_symbol')</th>
            <th>@lang('warehouse_orders.table.manufacturer_name')</th>
            <th>@lang('warehouse_orders.table.product_name_on_commercial_packing')</th>
            <th>@lang('warehouse_orders.table.ean_of_commercial_packing')</th>
            <th>@lang('warehouse_orders.table.product_name_on_collective_box')</th>
            <th>@lang('warehouse_orders.table.ean_of_collective_packing')</th>
            <th>@lang('warehouse_orders.table.quantity')</th>
            <th>@lang('warehouse_orders.table.commercial_free')</th>
            <th>@lang('warehouse_orders.table.number_of_sale_units_in_the_pack')</th>
            <th>@lang('warehouse_orders.table.full_packs')</th>
            <th>@lang('warehouse_orders.table.numbers_in_transport_pack')</th>
            <th>@lang('warehouse_orders.table.numbers_in_transport_pack_full')</th>
            <th>@lang('warehouse_orders.table.numbers_on_a_layer')</th>
            <th>@lang('warehouse_orders.table.number_of_layers_full')</th>
            <th>@lang('warehouse_orders.table.numbers_on_layer_last')</th>
            <th>@lang('warehouse_orders.table.alley')</th>
            <th>@lang('warehouse_orders.table.stillage')</th>
            <th>@lang('warehouse_orders.table.shelf')</th>
            <th>@lang('warehouse_orders.table.position')</th>
            <th>@lang('warehouse_orders.table.alley')</th>
            <th>@lang('warehouse_orders.table.stillage')</th>
            <th>@lang('warehouse_orders.table.shelf')</th>
            <th>@lang('warehouse_orders.table.position')</th>
            <th>@lang('warehouse_orders.table.alley')</th>
            <th>@lang('warehouse_orders.table.stillage')</th>
            <th>@lang('warehouse_orders.table.shelf')</th>
            <th>@lang('warehouse_orders.table.position')</th>
            <th>@lang('warehouse_orders.table.create_commercial')</th>
            <th>@lang('warehouse_orders.table.net_purchase_price_commercial_unit_after_discounts')</th>
            <th>@lang('warehouse_orders.table.net_purchase_price_commercial_unit')</th>
            <th>@lang('warehouse_orders.table.unit_commercial')</th>
            <th>@lang('warehouse_orders.table.create_collective')</th>
            <th>@lang('warehouse_orders.table.net_purchase_price_aggregate_unit_after_discounts')</th>
            <th>@lang('warehouse_orders.table.net_purchase_price_aggregate_unit')</th>
            <th>@lang('warehouse_orders.table.unit_of_collective')</th>
            <th>@lang('warehouse_orders.table.number_of_commercial_units_in_transport_pack')</th>
            <th>@lang('warehouse_orders.table.create_transport')</th>
            <th>@lang('warehouse_orders.table.net_purchase_price_the_largest_unit_after_discounts')</th>
            <th>@lang('warehouse_orders.table.net_purchase_price_the_largest_unit')</th>
            <th>@lang('warehouse_orders.table.unit_biggest')</th>
            <th>@lang('warehouse_orders.table.create_basic')</th>
            <th>@lang('warehouse_orders.table.net_purchase_price_basic_unit_after_discounts')</th>
            <th>@lang('warehouse_orders.table.net_purchase_price_basic_unit')</th>
            <th>@lang('warehouse_orders.table.unit_basic')</th>
            <th>@lang('warehouse_orders.table.create_calculation')</th>
            <th>@lang('warehouse_orders.table.net_purchase_price_calculation_unit_after_discounts')</th>
            <th>@lang('warehouse_orders.table.net_purchase_price_calculation_unit')</th>
            <th>@lang('warehouse_orders.table.calculation_unit')</th>
            <th>@lang('warehouse_orders.table.number_of_sale_units_in_the_pack')</th>
            <th>@lang('warehouse_orders.table.discount1')</th>
            <th>@lang('warehouse_orders.table.discount2')</th>
            <th>@lang('warehouse_orders.table.discount3')</th>

        </tr>
        </thead>
    </table>
@endsection

@section('datatable-scripts')
    <script>
        localStorage.setItem('products', JSON.stringify([]));
    
        const deleteRecord = (id) => {
            $('#delete_form')[0].action = "{{ url()->current() }}/" + id;
            $('#delete_modal').modal('show');
        };
        var breadcrumb = $('.breadcrumb:nth-child(2)');

        var visibility = {
            'true': '',
            'false': 'noVis'
        }

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='javascript:void();'>Zamówienie towaru</a></li>");

        $.fn.dataTable.ext.errMode = 'throw';


        // DataTable
        window.table = table = $('#dataTable-warehouseOrder').DataTable({
            language: {!! json_encode( __('voyager.datatable'), true) !!},
            processing: true,
            serverSide: true,
            stateSave: true,
            fnDrawCallback: function( oSettings ) {
                let products = JSON.parse(localStorage.getItem('products'));
                Object.keys(products).forEach(function (key) {
                    Object.keys(products[key]).forEach((function(item) {
                        $('input.itemQuantity[data-product="' + item + '"][data-type="commercial"]').val(products[key][item].quantity);
                        $('input.itemQuantity[data-product="' + item + '"][data-type="commercial"]').trigger('change');
                    }));
                });
            },
            "lengthMenu": [[10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500, "Wszystkie"]],
            columnDefs: [
                {className: "dt-center", targets: "_all"},
                {width: "20%", targets: 32,'max-width': '300px' }
            ],
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'pageLength',
                {
                    extend: 'colvis',
                    text: 'Widzialność kolumn',
                    columns: ':not(.noVis)'
                },
                {
                    extend: 'colvisGroup',
                    text: 'Pokaż wszystkie',
                    show: ':hidden'
                },
            ],
            ajax: {
                url: '{!! route('warehouse.orders.datatable') !!}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            },
            columns: [
                {
                    data: 'id',
                    name: 'id',
                },
                {
                    data: 'symbol',
                    name: 'symbol',
                    defaultContent: '',
                },
                {
                    data: 'name',
                    name: 'name',
                    defaultContent: '',
                },
                {
                    data: 'manufacturer',
                    name: 'manufacturer',
                    defaultContent: '',
                },
                {
                    data: 'product_name_manufacturer',
                    name: 'product_name_manufacturer',
                    defaultContent: '',
                },
                {
                    data: 'id',
                    name: 'product_name_on_commercial_packing',
                    defaultContent: '',
                },
                {
                    data: 'ean_of_commercial_packing',
                    name: 'ean_of_commercial_packing',
                    defaultContent: '',
                },
                {
                    data: 'product_name_on_collective_box',
                    name: 'product_name_on_collective_box',
                    defaultContent: '',
                },
                {
                    data: 'ean_of_collective_packing',
                    name: 'ean_of_collective_packing',
                    defaultContent: '',
                },
                {
                    data: 'quantity',
                    name: 'quantity',
                    defaultContent: '',
                },
                {
                    data: 'id',
                    name: 'commercial_free',
                    defaultContent: '',
                    render: function(data, type, row, dupa, x) {
                        let quantity = row.quantity;
                        let reserved = 0;

                        return quantity - reserved;
                    }
                },
                {
                    data: 'number_of_sale_units_in_the_pack',
                    name: 'number_of_sale_units_in_the_pack',
                    defaultContent: '',
                },
                {
                    data: 'id',
                    name: 'full_packs',
                    render: function(data, type, row) {
                        let quantity = row.quantity;
                        let unitsInPack = ~~parseInt(row.number_of_sale_units_in_the_pack);
                        if(unitsInPack == 0) {
                            return 0;
                        } else {
                            return Math.floor(quantity/unitsInPack);
                        }

                    }
                },
                {
                    data: 'numbers_of_basic_commercial_units_in_transport_pack',
                    name: 'numbers_of_basic_commercial_units_in_transport_pack',
                },
                {
                    data: 'id',
                    name: 'numbers_in_transport_pack_full',
                    render: function(data, type, row) {
                        let quantity = row.quantity;
                        let unitsInPack = ~~parseInt(row.numbers_of_basic_commercial_units_in_transport_pack);
                        if(unitsInPack == 0) {
                            return 0;
                        } else {
                            return Math.floor(quantity/unitsInPack);
                        }
                    }
                },
                {
                    data: 'numbers_on_a_layer',
                    name: 'numbers_on_a_layer',
                    defaultContent: '',
                },
                {
                    data: 'id',
                    name: 'number_of_layers_full',
                    render: function(data, type, row) {
                        let quantity = row.quantity;
                        let unitsInPack = ~~parseInt(row.number_of_sale_units_in_the_pack);
                        let numberOnLayer = row.numbers_on_a_layer;
                        let packUnits;
                        if(unitsInPack == 0) {
                            return 0;
                        } else {
                            packUnits = Math.floor(quantity/unitsInPack);
                        }

                        return Math.floor((quantity-(packUnits * unitsInPack))/numberOnLayer);
                    }
                },
                {
                    data: 'id',
                    name: 'numbers_on_layer_last',
                    render: function(data, type, row) {
                        let quantity = row.quantity;
                        let unitsInPack = ~~parseInt(row.number_of_sale_units_in_the_pack);
                        let numberOnLayer = row.numbers_on_a_layer;
                        let packUnits;
                        if(unitsInPack == 0) {
                            return 0;
                        } else {
                            packUnits = Math.floor(quantity/unitsInPack);
                        }

                        let layersCount  = Math.floor((quantity-(packUnits * unitsInPack))/numberOnLayer);

                        return quantity - (packUnits * unitsInPack) - (layersCount * row.numbers_on_a_layer)
                    }
                },
                {
                    data: 'id',
                    name: 'firstAlley',
                    defaultContent: '',
                    render: function(data, type, row) {
                        if(row.positions != null) {
                            if(row.positions[0] != null) {
                                return row.positions[0].lane;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'firstStillage',
                    defaultContent: '',
                    render: function(data, type, row) {
                        if(row.positions != null) {
                            if(row.positions[0] != null) {
                                return row.positions[0].bookstand;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'firstShelf',
                    defaultContent: '',
                    render: function(data, type, row) {
                        if(row.positions != null) {
                            if(row.positions[0] != null) {
                                return row.positions[0].shelf;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'firstPosition',
                    defaultContent: '',
                    render: function(data, type, row) {
                        if(row.positions != null) {
                            if(row.positions[0] != null) {
                                return row.positions[0].position;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'secondAlley',
                    defaultContent: '',
                    render: function(data, type, row) {
                        if(row.positions != null) {
                            if(row.positions[1] != null) {
                                return row.positions[1].lane;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'secondStillage',
                    defaultContent: '',
                    render: function(data, type, row) {
                        if(row.positions != null) {
                            if(row.positions[1] != null) {
                                return row.positions[1].bookstand;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'secondShelf',
                    defaultContent: '',
                    render: function(data, type, row) {
                        if(row.positions != null) {
                            if(row.positions[1] != null) {
                                return row.positions[1].shelf;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'secondPosition',
                    defaultContent: '',
                    render: function(data, type, row) {
                        if(row.positions != null) {
                            if(row.positions[1] != null) {
                                return row.positions[1].position;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'thirdAlley',
                    defaultContent: '',
                    render: function(data, type, row) {
                        if(row.positions != null) {
                            if(row.positions[2] != null) {
                                return row.positions[2].lane;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'thirdStillage',
                    defaultContent: '',
                    render: function(data, type, row) {
                        if(row.positions != null) {
                            if(row.positions[2] != null) {
                                return row.positions[2].bookshelf;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'thirdShelf',
                    defaultContent: '',
                    render: function(data, type, row) {
                        if(row.positions != null) {
                            if(row.positions[2] != null) {
                                return row.positions[2].shelf;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'thirdPosition',
                    defaultContent: '',
                    render: function(data, type, row) {
                        if(row.positions != null) {
                            if(row.positions[2] != null) {
                                return row.positions[2].position;
                            } else {
                                return '';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'create_commercial',
                    render: function(id, type, row) {
                        let html = '<input type="number" data-product="' + id + '" data-type="commercial" data-type="commercial" class="itemQuantity" name="commercialQuantity">';
                        let commercial = row.net_purchase_price_commercial_unit;
                        let commercialAfter = row.net_purchase_price_commercial_unit_after_discounts;
                        let basic = row.net_purchase_price_basic_unit;
                        let basicAfter = row.net_purchase_price_basic_unit_after_discounts;
                        let calculation = row.net_purchase_price_calculated_unit;
                        let calculationAfter = row.net_purchase_price_calculated_unit_after_discounts;
                        let collective = row.net_purchase_price_aggregate_unit;
                        let collectiveAfter = row.net_purchase_price_aggregate_unit_after_discounts;
                        let transport = row.net_purchase_price_the_largest_unit;
                        let transportAfter = row.net_purchase_price_the_largest_unit_after_discounts;
                        let numbers_of_basic_commercial_units_in_pack = row.numbers_of_basic_commercial_units_in_pack;
                        let unitConsumption = row.unit_consumption;
                        let number_of_sale_units_in_the_pack = row.number_of_sale_units_in_the_pack;
                        let number_of_trade_items_in_the_largest_unit = row.number_of_trade_items_in_the_largest_unit;
                        let weight_trade_unit = row.weight_trade_unit;
                        let discount1 = ~~parseFloat(row.discount1);
                        let discount2 = ~~parseFloat(row.discount2);
                        let discount3 = ~~parseFloat(row.discount3);
                        let warehouse = row.manufacturer;


                        let number_of_trade_items_in_the_largest_unitInput = '<input type="hidden" data-product="' + id + '" name="number_of_trade_items_in_the_largest_unit" value="' + number_of_trade_items_in_the_largest_unit + '">';
                        let numbers_of_basic_commercial_units_in_packInput = '<input type="hidden" data-product="' + id + '" name="numbers_of_basic_commercial_units_in_pack" value="' + numbers_of_basic_commercial_units_in_pack + '">';
                        let number_of_sale_units_in_the_packInput = '<input type="hidden" data-product="' + id + '" name="number_of_sale_units_in_the_pack" value="' + number_of_sale_units_in_the_pack + '">';
                        let unitConsumptionInput = '<input type="hidden" data-product="' + id + '" name="unit_consumption" value="' + unitConsumption + '">';
                        let commercialInput = '<input type="hidden" data-product="' + id + '" data-base="' + commercial + '" data-discount="' + commercialAfter + '" data-type="commercial" name="commercialPrices">';
                        let basicInput = '<input type="hidden"  data-product="' + id + '"  data-base="' + basic + '" data-discount="' + basicAfter + '" data-type="basic" name="basicPrices">';
                        let calculationInput = '<input type="hidden"  data-product="' + id + '"  data-base="' + calculation + '" data-discount="' + calculationAfter + '" data-type="calculation" name="calculationPrices">';
                        let collectiveInput = '<input type="hidden"  data-product="' + id + '"  data-base="' + collective + '" data-discount="' + collectiveAfter + '" data-type="collective" name="collectivePrices">';
                        let transportInput = '<input type="hidden"  data-product="' + id + '"  data-base="' + transport + '" data-discount="' + transportAfter + '" data-type="transport" name="transportPrices">';
                        let weight_trade_unitInput = '<input type="hidden"  data-product="' + id + '" name="productWeight" value="' + weight_trade_unit + '">';
                        let discount1Input = '<input type="hidden"  data-product="' + id + '" name="discount1" value="' + discount1 + '">';
                        let discount2Input = '<input type="hidden"  data-product="' + id + '" name="discount2" value="' + discount2 + '">';
                        let discount3Input = '<input type="hidden"  data-product="' + id + '" name="discount3" value="' + discount3 + '">';
                        let warehouseInput = '<input type="hidden"  data-product="' + id + '" name="warehouse" value="' + warehouse + '">';

                        html += commercialInput + basicInput + calculationInput + collectiveInput + transportInput + numbers_of_basic_commercial_units_in_packInput + unitConsumptionInput + number_of_sale_units_in_the_packInput + number_of_trade_items_in_the_largest_unitInput + weight_trade_unitInput + discount1Input + discount2Input + discount3Input + warehouseInput;

                        return html;
                    }
                },
                {
                    data: 'net_purchase_price_commercial_unit_after_discounts',
                    name: 'net_purchase_price_commercial_unit_after_discounts',
                    defaultContent: '',
                    render: function(price, type, row) {
                        let html = '<input type="text" data-product="' + row.id + '" data-type="commercialAfter" class="itemPrice" name="commercialPriceAfter" value="' + price +'">';

                        return html;
                    }
                },
                {
                    data: 'net_purchase_price_commercial_unit',
                    name: 'net_purchase_price_commercial_unit',
                    defaultContent: '',
                    render: function(price, type, row) {
                        let html = '<input type="text" data-product="' + row.id + '" data-type="commercial" class="itemPrice" name="commercialPrice" value="' + price +'">';

                        return html;
                    }
                },
                {
                    data: 'unit_commercial',
                    name: 'unit_commercial',
                    defaultContent: '',
                },
                {
                    data: 'id',
                    name: 'create_collective',
                    render: function(id) {
                        let html = '<input type="number" data-product="' + id + '" class="itemQuantity" data-type="collective" name="collectiveQuantity">';
                        return html;
                    }
                },
                {
                    data: 'net_purchase_price_aggregate_unit_after_discounts',
                    name: 'net_purchase_price_aggregate_unit_after_discounts',
                    defaultContent: '',
                    render: function(price, type, row) {
                        let html = '<input type="text" data-product="' + row.id + '" data-type="aggregateAfter" class="itemPrice" name="aggregatePriceAfter" value="' + price +'">';

                        return html;
                    }
                },
                {
                    data: 'net_purchase_price_aggregate_unit',
                    name: 'net_purchase_price_aggregate_unit',
                    defaultContent: '',
                    render: function(price, type, row) {
                        let html = '<input type="text" data-product="' + row.id + '" data-type="aggregate" class="itemPrice" name="aggregatePrice" value="' + price +'">';

                        return html;
                    }
                },
                {
                    data: 'unit_of_collective',
                    name: 'unit_of_collective',
                    defaultContent: '',
                },
                {
                    data: 'number_of_trade_items_in_the_largest_unit',
                    name: 'number_of_trade_items_in_the_largest_unit',
                },
                {
                    data: 'id',
                    name: 'create_transport',
                    render: function(id) {
                        let html = '<input type="number" data-product="' + id + '" class="itemQuantity" data-type="transport" name="transportQuantity">';
                        return html;
                    }
                },
                {
                    data: 'net_purchase_price_the_largest_unit_after_discounts',
                    name: 'net_purchase_price_the_largest_unit_after_discounts',
                    render: function(price, type, row) {
                        let html = '<input type="text" data-product="' + row.id + '" data-type="transportAfter" class="itemPrice" name="transportPriceAfter" value="' + price +'">';

                        return html;
                    }
                },
                {
                    data: 'net_purchase_price_the_largest_unit',
                    name: 'net_purchase_price_the_largest_unit',
                    render: function(price, type, row) {
                        let html = '<input type="text" data-product="' + row.id + '" data-type="transport" class="itemPrice" name="transportPrice" value="' + price +'">';

                        return html;
                    }
                },
                {
                    data: 'unit_biggest',
                    name: 'unit_biggest',
                },
                {
                    data: 'id',
                    name: 'create_basicd',
                    render: function(id) {
                        let html = '<input type="number" data-product="' + id + '" class="itemQuantity" data-type="basic" name="basicQuantity">';
                        return html;
                    }
                },
                {
                    data: 'net_purchase_price_basic_unit_after_discounts',
                    name: 'net_purchase_price_basic_unit_after_discounts',
                    defaultContent: '',
                    render: function(price, type, row) {
                        let html = '<input type="text" data-product="' + row.id + '" data-type="basicAfter" class="itemPrice" name="basicPriceAfter" value="' + price +'">';

                        return html;
                    }
                },
                {
                    data: 'net_purchase_price_basic_unit',
                    name: 'net_purchase_price_basic_unit',
                    defaultContent: '',
                    render: function(price, type, row) {
                        let html = '<input type="text" data-product="' + row.id + '" data-type="basic" class="itemPrice" name="basicPrice" value="' + price +'">';

                        return html;
                    }
                },
                {
                    data: 'unit_basic',
                    name: 'unit_basic',
                    defaultContent: '',
                },
                {
                    data: 'id',
                    name: 'create_calculation',
                    render: function(id) {
                        let html = '<input type="number" data-product="' + id + '" class="itemQuantity" data-type="calculation" name="calculationQuantity">';
                        return html;
                    }
                },
                {
                    data: 'net_purchase_price_calculated_unit_after_discounts',
                    name: 'net_purchase_price_calculated_unit_after_discounts',
                    defaultContent: '',
                    render: function(price, type, row) {
                        let html = '<input type="text" data-product="' + row.id + '" data-type="calculationAfter" class="itemPrice" name="calculationPriceAfter" value="' + price +'">';

                        return html;
                    }
                },
                {
                    data: 'net_purchase_price_calculated_unit',
                    name: 'net_purchase_price_calculated_unit',
                    defaultContent: '',
                    render: function(price, type, row) {
                        console.log(row);
                        let html = '<input type="text" data-product="' + row.id + '" data-type="calculation" class="itemPrice" name="calculationPrice" value="' + price +'">';

                        return html;
                    }
                },
                {
                    data: 'calculation_unit',
                    name: 'calculation_unit',
                    defaultContent: '',
                },
                {
                    data: 'number_of_sale_units_in_the_pack',
                    name: 'number_of_sale_units_in_the_pack',
                    defaultContent: '',
                },
                {
                    data: 'discount1',
                    name: 'discount1',
                },
                {
                    data: 'discount2',
                    name: 'discount2',
                },
                {
                    data: 'discount3',
                    name: 'discount3',
                },
            ],
        });

        @foreach($visibilities as $key =>$row)
            var {{'show'.$row->name}}  = @json($row->show);
            {{'show'.$row->name}} = {{'show'.$row->name}}.map(function(x){
                // if (typeof table.column(x+':name').index() === "number")
                return table.column(x+':name').index();
            });
            {{'show'.$row->name}} = {{'show'.$row->name}}.filter(function (el) {
                return el != null;
            });

            var {{'hidden'.$row->name}} = @json($row->hidden);
            {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.map(function(x){
                // if (typeof table.column(x+':name').index() === "number")
                return table.column(x+':name').index();
            });
            {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.filter(function (el) {
                return el != null;
            });

            console.log({{'hidden'.$row->name}});
            console.log({
                extend: 'colvisGroup',
                text: '{{$row->display_name}}',
                show: {{'show'.$row->name}},
                hide: {{'hidden'.$row->name}}
            });
            table.button().add({{1+$key}},{
                extend: 'colvisGroup',
                text: '{{$row->display_name}}',
                show: {{'show'.$row->name}},
                hide: {{'hidden'.$row->name}}
            });
        @endforeach

        $('#dataTable-warehouseOrder thead tr:nth-child(2) th').each(function (i) {
            var title = $(this).text();
            if (title !== '' && title !== 'Akcje') {
                let notSearchable = [17, 19];
                let localDatatables = localStorage.getItem('DataTables_dataTable_/admin/orders');
                let objDatatables = JSON.parse(localDatatables);
                if (title == "Podaj ilość zamówienia w jedn. zbior." || title == "Pod aj ilo ść zamów ienia w jedn. handl." || title == "Podaj ilość zamówienia w jedn. transp." || title == "Podaj ilość zamówienia w jedn. podst." || title == 'Podaj ilość zamówienia w jedn. oblicz.' ) {
                    $(this).html('<div><span>' + title + '</span><button class="btn btn-success" name="makeWarehouseOrder">Stwórz zlecenie</button></div>');
                } else if(~notSearchable.indexOf(i) == false) {
                    $(this).html('<div><span>'+title+'</span></div><div class="input_div"><input type="text" id="columnSearch' + i + '"/></div>');
                }
            } else if(title == 'Akcje') {
                $(this).html('<span id="columnSearch' + i + '">Akcje</span>');
            }
            $('input', this).on('change', function () {
                if(table.column(i).search() !== this.value) {
                    table
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        

        $('#orderFilter').change(function () {
            if(this.value == 'ALL') {
                table
                    .search( '' )
                    .columns().search( '' )
                    .draw();
            } else {
                table
                    .columns(9)
                    .search( 'przyjete zapytanie ofertowe|w trakcie analizowania przez konsultanta|mozliwa do realizacji|mozliwa do realizacji kominy|w trakcie realizacji|oferta zakonczona|oferta oczekujaca|oferta bez realizacji', true, false )
                    .draw();
            }

        });

        $('#dataTable-warehouseOrder').on( 'column-visibility.dt', function ( e, settings, column, state ) {
            if(state == true) {
                $("#columnSearch" + column).parent().show();
            } else {
                $("#columnSearch" + column).parent().hide();
            }
            table.columns.adjust();

        });

        function clearFilters(reload = true) {
            $("#columnSearch-shipment_date").val("all");
            $("#columnSearch-packages_not_sent").val("");
            $("#columnSearch-packages_sent").val("");
            $("#dataTable-warehouseOrder thead tr input").val("");
            $(".filter-by-labels-in-group__clear button").click();
            $('#searchLP').val('');
            $('#searchOrderValue').val('');
            $('#searchPayment').val('');
            $('#searchLeft').val('');

            let statusSearch = table.columns('statusName:name').search()[0];

            $(this).find("input").val("");
            table
                .columns()
                .search('');

            table.columns('statusName:name').search(statusSearch);

            if (reload) {
                table.draw();
            }
        }

    </script>
    <script>
        function updateQuantities(id, quantity, type)
        {
            let numberBasicInPack = parseFloat($('input[name="numbers_of_basic_commercial_units_in_pack"][data-product="' + id + '"]').val());
            let unitConsumption = parseFloat($('input[name="unit_consumption"][data-product="' + id + '"]').val());
            let numberSaleInPack = parseFloat($('input[name="number_of_sale_units_in_the_pack"][data-product="' + id + '"]').val());
            let numberTradeInPack = parseFloat($('input[name="number_of_trade_items_in_the_largest_unit"][data-product="' + id + '"]').val());
            switch(type){
                case 'commercial':
                    $('input.itemQuantity[data-product="' + id + '"][data-type="collective"]').val(quantity/numberSaleInPack);
                    $('input.itemQuantity[data-product="' + id + '"][data-type="transport"]').val(quantity / numberTradeInPack);
                    $('input.itemQuantity[data-product="' + id + '"][data-type="basic"]').val(quantity * numberBasicInPack);
                    $('input.itemQuantity[data-product="' + id + '"][data-type="calculation"]').val((quantity*numberBasicInPack)/unitConsumption);
                    break;
                case 'collective':
                    $('input.itemQuantity[data-product="' + id + '"][data-type="commercial"]').val(quantity * numberSaleInPack);
                    $('input.itemQuantity[data-product="' + id + '"][data-type="commercial"]').trigger('change');
                    break;
                case 'transport':
                    $('input.itemQuantity[data-product="' + id + '"][data-type="commercial"]').val(quantity * numberTradeInPack);
                    $('input.itemQuantity[data-product="' + id + '"][data-type="commercial"]').trigger('change');
                    break;
                case 'basic':
                    $('input.itemQuantity[data-product="' + id + '"][data-type="commercial"]').val(quantity / numberBasicInPack);
                    $('input.itemQuantity[data-product="' + id + '"][data-type="commercial"]').trigger('change');
                    break;
                case 'calculation':
                    $('input.itemQuantity[data-product="' + id + '"][data-type="commercial"]').val((quantity * unitConsumption)/numberBasicInPack);
                    $('input.itemQuantity[data-product="' + id + '"][data-type="commercial"]').trigger('change');
                    break;
            }
        }
        $(document).on('change', 'input.itemQuantity', function() {
            let productId = $(this).data('product');
            let productQuantity = parseInt($(this).val());
            if($(this).val() == 0) {
                let products = JSON.parse(localStorage.getItem('products'));
                delete products.find(x => x == productId);
                localStorage.setItem('products', JSON.stringify(products));
            } else {
                let weight = $('input[name="productWeight"][data-product="' + productId + '"]').val();
                let commercial = $('input[name="commercialPrice"][data-product="' + productId + '"]').val();
                let commercialAfter = $('input[name="commercialPriceAfter"][data-product="' + productId + '"]').val();
                let basic = $('input[name="basicPrice"][data-product="' + productId + '"]').val();
                let basicAfter = $('input[name="basicPriceAfter"][data-product="' + productId + '"]').val();
                let calculation = $('input[name="calculationPrice"][data-product="' + productId + '"]').val();
                let calculationAfter = $('input[name="calculationPriceAfter"][data-product="' + productId + '"]').val();
                let collective = $('input[name="aggregatePrice"][data-product="' + productId + '"]').val();
                let collectiveAfter = $('input[name="aggregatePriceAfter"][data-product="' + productId + '"]').val();
                let transport = $('input[name="transportPrice"][data-product="' + productId + '"]').val();
                let transportAfter = $('input[name="transportPriceAfter"][data-product="' + productId + '"]').val();
                let warehouse = $('input[name="warehouse"][data-product="' + productId + '"]').val();
                let products = JSON.parse(localStorage.getItem('products'));
                updateQuantities(productId, productQuantity, $(this).data('type'));
                let flag = false;
                products.forEach(function(product) {
                    if(product[productId] != undefined) {
                        flag = true;
                    }
                });
                if(flag == false) {
                    let productQuantity = parseInt($('input[name="commercialQuantity"][data-product="' + productId + '"]').val());
                    let element = {
                        [productId] : {
                            'warehouse': warehouse,
                            'quantity': productQuantity,
                            'commercial': parseFloat(commercial),
                            'commercialAfter': parseFloat(commercialAfter),
                            'basic': parseFloat(basic),
                            'basicAfter': parseFloat(basicAfter),
                            'calculation': parseFloat(calculation),
                            'calculationAfter': parseFloat(calculationAfter),
                            'collective': parseFloat(collective),
                            'collectiveAfter': parseFloat(collectiveAfter),
                            'transport': parseFloat(transport),
                            'transportAfter': parseFloat(transportAfter),
                            'weight': parseFloat(weight),
                        }
                    };
                    products.push(element);
                    localStorage.setItem('products', JSON.stringify(products));
                    updateGlobalValues();
                } else {
                    console.log('ttt');
                    products.forEach(function(product) {
                        if(product[productId] != undefined) {
                            product[productId]['warehouse'] = warehouse;
                            product[productId]['quantity'] = productQuantity;
                            product[productId]['commercial'] = parseFloat(commercial);
                            product[productId]['commercialAfter'] = parseFloat(commercialAfter);
                            product[productId]['basic'] = parseFloat(basic);
                            product[productId]['basicAfter'] = parseFloat(basicAfter);
                            product[productId]['calculation'] = parseFloat(calculation);
                            product[productId]['calculationAfter'] = parseFloat(calculationAfter);
                            product[productId]['collective'] = parseFloat(collective);
                            product[productId]['collectiveAfter'] = parseFloat(collectiveAfter);
                            product[productId]['transport'] = parseFloat(transport);
                            product[productId]['transportAfter'] = parseFloat(transportAfter);
                            product[productId]['weight'] = parseFloat(weight);
                        }
                        console.log(product);
                    });
                    localStorage.setItem('products', JSON.stringify(products));
                }
            }
        });

        $(document).on('change', '.itemPrice', function(){
            let productId = this.dataset.product;
            let discount1 = ~~parseFloat($('input[name="discount1"][data-product="' + productId + '"]').val());
            let discount2 = ~~parseFloat($('input[name="discount2"][data-product="' + productId + '"]').val());
            let discount3 = ~~parseFloat($('input[name="discount3"][data-product="' + productId + '"]').val());
            let itemsInPack = ~~parseFloat($('input[name="number_of_sale_units_in_the_pack"][data-product="' + productId + '"]').val());
            let type = this.dataset.type;
            let value = this.value;
            updatePrices(productId, value, itemsInPack, discount1, discount2, discount3);
            console.log(productId);
            console.log(type);
            let products = JSON.parse(localStorage.getItem('products'));
            products.forEach(function(product) {
                if(product[productId] != undefined) {
                    product[productId][type] = parseFloat(value);
                }
            });
            localStorage.setItem('products', JSON.stringify(products));
        });

        function updateGlobalValues(){
            let orderWeight = 0;
            let orderValue = 0;
            let products = JSON.parse(localStorage.getItem('products'));
            products.forEach(function(product) {
                Object.keys(product).forEach(function (key) {
                    if(key != undefined) {
                        orderValue += product[key].quantity * product[key].commercialAfter;
                        orderWeight += product[key].quantity * product[key].weight;
                    }
                });
            });

            console.log(orderValue);
            console.log(orderWeight);
            $('#orderValue').text(orderValue + ' zł');
            $('#orderWeight').text(orderWeight + ' kg');
        }

        $('[name="makeWarehouseOrder"]').on('click', function() {
            let products = localStorage.getItem('products');
            $.ajax({
                type: "POST",
                url: '{!! route('warehouse.orders.makeOrder') !!}',
                data: {'products': products},
            }).done(function(data) {
                window.location.replace(data);
            });
        });

        function updatePrices(productId, price, itemsInPack, discount1, discount2, discount3)
        {
            let priceAfterDiscounts = (price * ((100-discount1)*(100-discount2)*(100-discount3)/1000000)).toFixed(4);
            console.log(priceAfterDiscounts);
            $('input[name="commercialPrice"][data-product="' + productId + '"]').val(price);
            $('input[name="commercialPriceAfter"][data-product="' + productId + '"]').val(priceAfterDiscounts);
            $('input[name="basicPrice"][data-product="' + productId + '"]').val(price);
            $('input[name="basicPriceAfter"][data-product="' + productId + '"]').val(priceAfterDiscounts);
            $('input[name="calculationPrice"][data-product="' + productId + '"]').val(price);
            $('input[name="calculationPriceAfter"][data-product="' + productId + '"]').val(priceAfterDiscounts);
            $('input[name="aggregatePrice"][data-product="' + productId + '"]').val(price * itemsInPack);
            $('input[name="aggregatePriceAfter"][data-product="' + productId + '"]').val(priceAfterDiscounts * itemsInPack);
            $('input[name="transportPrice"][data-product="' + productId + '"]').val(price);
            $('input[name="transportPriceAfter"][data-product="' + productId + '"]').val(priceAfterDiscounts);
        }
    </script>

@endsection
