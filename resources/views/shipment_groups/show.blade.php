@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> Grupa przesyłek
        <a href="{!! route('shipment-groups.create') !!}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>Dodaj przesyłkę do grupy</span>
        </a>
    </h1>
@endsection

@section('table')
    <div class="row">
        <div class="col-md-12">
            <h4>Szczegóły przesyłki</h4>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-4 col-md-offset-2">
                    Nazwa kuriera
                </div>
                <div class="col-md-3 text-center" style="border-bottom: 0.15em dotted black">
                    {{ $shipmentGroup->courier_name }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-md-offset-2">
                    Typ paczki
                </div>
                <div class="col-md-3 text-center" style="border-bottom: 0.15em dotted black">
                    {{ $shipmentGroup->package_type ?? 'Standard' }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-md-offset-2">
                    Lp
                </div>
                <div class="col-md-3 text-center" style="border-bottom: 0.15em dotted black">
                    {{ $shipmentGroup->lp }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-4 col-md-offset-2">
                    Data wysyłki
                </div>
                <div class="col-md-3 text-center" style="border-bottom: 0.15em dotted black">
                    {{ $shipmentGroup->shipment_date->format('Y-m-d') }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-md-offset-2">
                    Wysłanie
                </div>
                <div class="col-md-3 text-center" style="border-bottom: 0.15em dotted black">
                    {{ $shipmentGroup->sent  ? 'Wysłane' : 'Nie wysłane'}}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-md-offset-2">
                    Zamknięcie
                </div>
                <div class="col-md-3 text-center" style="border-bottom: 0.15em dotted black">
                    {{ $shipmentGroup->closed ? 'Zamknięta' : 'Otwarta'}}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('shipment-groups.print',['id'=>$shipmentGroup->id]) }}">
                <button class="btn btn-success" type="submit">Drukuj spis</button>
            </form>
        </div>
    </div>

    <div class="order-packages" id="order-packages">
        <table style="width: 100%" id="dataTableOrderPackages" class="table table-hover">
            <thead>
            <tr>
                <th>ID</th>
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

    <div class="modal fade" tabindex="-1" id="add_bonus_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Dodaj potrącenie</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="submit" form="add_new_bonus_form" class="btn btn-success pull-right">Utwórz
                    </button>
                    <button type="button" class="btn btn-default pull-right"
                            data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('datatable-scripts')
    <script>
        const deleteRecordOrderPackages = (id) => {
            $('#delete_form')[0].action = '/shipment-groups/' + {!! $shipmentGroup->id !!} + '/remove-package/' + id;
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
                    name: 'id',
                    render: function (id, data, row) {
                        return row.order_id + '/' + row.number;
                    }
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
        // $('#add_bonus_modal').modal('show');

    </script>
@endsection
