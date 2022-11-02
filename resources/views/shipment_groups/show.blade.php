@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('statuses.title')
        <a href="{!! route('shipment-groups.create') !!}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>@lang('statuses.create')</span>
        </a>
    </h1>
@endsection

@section('table')
    <div class="order-packages" id="order-packages">
        <table style="width: 100%" id="dataTableOrderPackages" class="table table-hover">
            <thead>
            <tr>
                <th>ID</th>
                <th>@lang('order_packages.table.number')</th>
                <th>@lang('order_packages.table.status')</th>
                <th>@lang('order_packages.table.letter_number')</th>
                <th>@lang('order_packages.table.delivery_courier_name')</th>
                <th>@lang('order_packages.table.service_courier_name')</th>
                <th>@lang('order_packages.table.shipment_date')</th>
                <th>@lang('order_packages.table.delivery_date')</th>
                <th>@lang('order_packages.table.sending_number')</th>
                <th>@lang('voyager.generic.actions')</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection


@section('datatable-scripts')
    <script>
        const deleteRecordOrderPackages = (id) => {
            $('#delete_form')[0].action = "/admin/shipment-groups/" + {!! $shipmentGroup->id !!} + "/remove-package/" + id;
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
            ajax: '{!! route('shipment-groups.packageDatatable', ['id' => $shipmentGroup->id]) !!}',
            dom: 'Bfrtip',
            buttons: [
                {extend: 'colvis', text: 'Widzialność kolumn'}
            ],
            columns: [
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
                    render: function (status) {
                        if (status === 'DELIVERED') {
                            return '<span style="color: green;">' + {!! json_encode(__('order_packages.table.delivered'), true) !!} + '</span>';
                        } else if (status === 'CANCELLED') {
                            return '<span style="color: red;">' + {!! json_encode(__('order_packages.table.cancelled'), true) !!} + '</span>';
                        } else if (status === 'NEW') {
                            return '<span style="color: blue;">' + {!! json_encode(__('order_packages.table.new'), true) !!} + '</span>';
                        } else if (status === 'SENDING') {
                            return '<span style="color: orange;">' + {!! json_encode(__('order_packages.table.sending'), true) !!} + '</span>';
                        } else if (status === 'WAITING_FOR_SENDING') {
                            return '<span style="color: orange;">' + {!! json_encode(__('order_packages.table.waiting_for_sending'), true) !!} + '</span>';
                        } else if (status === 'WAITING_FOR_CANCELLED') {

                            return '<span style="color: orange;">' + {!! json_encode(__('order_packages.table.waiting_for_cancelled'), true) !!} + '</span>';
                        } else if (status === 'REJECT_CANCELLED') {

                            return '<span style="color: red;">Anulacja odrzucona</span>';
                        }
                    }
                },
                {
                    data: 'letter_number',
                    name: 'letter_number',
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
                    data: 'sending_number',
                    name: 'sending_number'
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function (id, data, row) {
                        let html = '';
                        if (row.status !== 'SENDING' && row.status !== 'WAITING_FOR_SENDING' && row.status !== 'CANCELLED' && row.status !== 'WAITING_FOR_CANCELLED' && row.status !== 'DELIVERED' && row.service_courier_name !== 'GIELDA' && row.service_courier_name !== 'ODBIOR_OSOBISTY' && row.delivery_courier_name !== 'GIELDA' && row.delivery_courier_name !== 'ODBIOR_OSOBISTY') {
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
                }
            ]
        });
        @foreach($visibilities as $key =>$row)

        var {{'show'.$row->name}}  = @json($row->show);
        {{'show'.$row->name}} = {{'show'.$row->name}}.map(function (x) {
            if (typeof tableOrderPackages.column(x + ':name').index() === "number")
                return tableOrderPackages.column(x + ':name').index();
        });
        {{'show'.$row->name}} = {{'show'.$row->name}}.filter(function (el) {
            return el != null;
        });

        var {{'hidden'.$row->name}} = @json($row->hidden);
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.map(function (x) {
            if (typeof tableOrderPackages.column(x + ':name').index() === "number")
                return tableOrderPackages.column(x + ':name').index();
        });
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.filter(function (el) {
            return el != null;
        });

        tableOrderPackages.button().add({{1+$key}}, {
            extend: 'colvisGroup',
            text: '{{$row->display_name}}',
            show: {{'show'.$row->name}},
            hide: {{'hidden'.$row->name}}
        });
        @endforeach

        $('#dataTableOrderPackages thead tr th').each(function (i) {
            var title = $(this).text();
            if (title !== '' && title !== 'Akcje') {
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
        });
    </script>
@endsection
