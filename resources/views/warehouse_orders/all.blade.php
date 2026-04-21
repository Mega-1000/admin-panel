@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-window-list"></i> @lang('warehouse_orders.title')
    </h1>
    <style>
        .pointer {
            cursor: pointer;
        }
        .ui-tooltip {
            width: 400px !important;
        }
    </style>
@endsection

@section('table')
    <table id="dataTable" class="table table-hover spacious-container">
        <thead>
        <tr>
            <th>@lang('warehouse_orders.table.created_at')</th>
            <th>@lang('warehouse_orders.table.id')</th>
            <th>@lang('warehouse_orders.table.company')</th>
            <th>@lang('warehouse_orders.table.status')</th>
            <th>@lang('warehouse_orders.table.warehouse_comment')</th>
            <th>@lang('warehouse_orders.table.email')</th>
            <th>@lang('warehouse_orders.table.shipment_date')</th>
            <th>Akcje</th>
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
        let table = $('#dataTable').DataTable({
            language: {!! json_encode( __('voyager.datatable'), true) !!},
            processing: true,
            serverSide: true,
            stateSave: true,
            "lengthMenu": [[10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500, "Wszystkie"]],
            columnDefs: [
                {className: "dt-center", targets: "_all"},
            ],
            responsive: true,
            ajax: {
                url: '{!! route('warehouse.orders.datatable.all') !!}',
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
                    data: 'created_at',
                    name: 'created_at',
                },
                {
                    data: 'company',
                    name: 'company',
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'warehouse_comment',
                    name: 'warehouse_comment',
                },
                {
                    data: 'email',
                    name: 'email',
                },
                {
                    data: 'shipment_date',
                    name: 'shipment_date',
                },
                {
                    data: 'id',
                    name: 'actions',
                    render: function (id) {
                        let html = '';
                        html += '<a href="{{ url()->current() }}/' + id + '/edit" class="btn btn-sm btn-primary edit">';
                        html += '<i class="voyager-edit"></i>';
                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>';
                        html += '</a>';
                        @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                            html += '<button class="btn btn-sm btn-danger delete delete-record" onclick="deleteRecord(' + id + ')">';
                        html += '<i class="voyager-trash"></i>';
                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>';
                        html += '</button>';
                        @endif

                            return html;
                    }
                },
            ],
        });

        $('#dataTable thead tr th').each(function (i) {
            var title = $(this).text();
            if (title !== '' && title !== 'Akcje') {
                let notSearchable = [17, 19];
                let localDatatables = localStorage.getItem('DataTables_dataTable_/admin/orders');
                let objDatatables = JSON.parse(localDatatables);
                if (i == 7) {
                    $(this).html('<div><span>' + title + '</span><button class="btn btn-success" name="makeWarehouseOrder">Stwórz zlecenie</button></div>');
                } else if (i == 17) {
                    $(this).html('<div><span>'+title+'</span></div><div class="input_div"><input type="number" style="color: red;" disabled="disabled" name="grossValue"/></div>');
                } else if (i == 19) {
                    $(this).html('<div><span>'+title+'</span></div><div class="input_div"><input type="number" style="color: red;" disabled="disabled" name="weightValue"/></div>');
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

        $('#dataTable').on( 'column-visibility.dt', function ( e, settings, column, state ) {
            if(state == true) {
                $("#columnSearch" + column).parent().show();
            } else {
                $("#columnSearch" + column).parent().hide();
            }

        });
        
       

    </script>
    <script>
        $(document).on('change', 'input[name="itemQuantity"]', function() {
            let productId = $(this).data('product');
            let productQuantity = parseInt($(this).val());
            if($(this).val() == 0) {
                let products = JSON.parse(localStorage.getItem('products'));
                console.log(productId);
                console.log(products.find(x => x == productId));
                delete products.find(x => x == productId);
                localStorage.setItem('products', JSON.stringify(products));
            } else {
                let products = JSON.parse(localStorage.getItem('products'));
                console.log(productId);
                console.log(products);
                let element = {
                        [productId] : productQuantity
                    };
                products.push(element);
                localStorage.setItem('products', JSON.stringify(products));
            }
        });

        $('[name="makeWarehouseOrder"]').on('click', function() {
            let products = localStorage.getItem('products');
            console.log(products);
            $.ajax({
                type: "POST",
                url: '{!! route('warehouse.orders.makeOrder') !!}',
                data: {'products': products},
            }).done(function(data) {
                window.location.replace(data);
            });
        });
    </script>

@endsection
