@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-people"></i> @lang('customers.title')
        <a href="{!! route('customers.create') !!}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>@lang('customers.create')</span>
        </a>
    </h1>
@endsection

@section('table')
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th>@lang('customers.table.login')</th>
            <th>@lang('customers.table.nick_allegro')</th>
            <th>@lang('customers.table.firstname')</th>
            <th>@lang('customers.table.lastname')</th>
            <th>@lang('customers.table.firmname')</th>
            <th>@lang('customers.table.nip')</th>
            <th>@lang('customers.table.phone')</th>
            <th>@lang('customers.table.address')</th>
            <th>@lang('customers.table.flat_number')</th>
            <th>@lang('customers.table.city')</th>
            <th>@lang('customers.table.postal_code')</th>
            <th>@lang('customers.table.email')</th>
            <th>@lang('customers.table.status')</th>
            <th>@lang('customers.table.created_at')</th>
            <th>@lang('voyager.generic.actions')</th>
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

        var visibility = {
            'true': '',
            'false': 'noVis'
        }

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='javascript:void();'>Klienci</a></li>");

        $.fn.dataTable.ext.errMode = 'throw';


        // DataTable
        let table = $('#dataTable').DataTable({
            language: {!! json_encode( __('voyager.datatable'), true) !!},
            processing: true,
            serverSide: true,

            columnDefs: [
                {className: "dt-center", targets: "_all"},
            ],
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'colvis',
                    text: 'Widzialność kolumn',
                    columns: ':not(.noVis)'
                }


            ],
            order: [[1, "asc"]],
            ajax: {
                type: 'GET',
                url:'{!! route('customers.datatable') !!}',

                /* tymczasowy fix bo to wogólę nie działało niech ktoś na to zobaczy


                 data: function (d) {
                     $.each(d, function( key, value ) {
                         if d.name = login
                             d.table = customers

                     });
                 }
*/

            },
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    render: function (id) {
                        return '<input type="checkbox">';
                    },
                },
                {
                    data: 'id',
                    name: 'id',
                    table: 'customers',
                },
                {
                    data: 'login',
                    name: 'login',
                    searchable: true

                },
                {
                    data: 'nick_allegro',
                    name: 'nick_allegro',
                },
                {
                    data: 'firstname',
                    name: 'customer_adresses.firstname',
                    visible: {{ App\Helpers\Helper::checkRole('customers', 'standard_firstname') }},
                    className: visibility["{{ App\Helpers\Helper::checkRole('customers', 'standard_firstname') }}"],
                    searchable: true
                },
                {
                    data: 'lastname',
                    name: 'lastname',
                    visible: {{ App\Helpers\Helper::checkRole('customers', 'standard_lastname') }},
                    className: visibility["{{ App\Helpers\Helper::checkRole('customers', 'standard_lastname') }}"],
                },
                {
                    data: 'firmname',
                    name: 'firmname',
                    visible: {{ App\Helpers\Helper::checkRole('customers', 'standard_firmname') }},
                    className: visibility["{{ App\Helpers\Helper::checkRole('customers', 'standard_firmname') }}"],
                },
                {
                    data: 'nip',
                    name: 'nip',
                    visible: {{ App\Helpers\Helper::checkRole('customers', 'standard_nip') }},
                    className: visibility["{{ App\Helpers\Helper::checkRole('customers', 'standard_nip') }}"],
                },
                {
                    data: 'phone',
                    name: 'phone',
                    visible: {{ App\Helpers\Helper::checkRole('customers', 'standard_phone') }},
                    className: visibility["{{ App\Helpers\Helper::checkRole('customers', 'standard_phone') }}"],
                },
                {
                    data: 'address',
                    name: 'address',
                    visible: {{ App\Helpers\Helper::checkRole('customers', 'standard_address') }},
                    className: visibility["{{ App\Helpers\Helper::checkRole('customers', 'standard_address') }}"],
                },
                {
                    data: 'flat_number',
                    name: 'flat_number',
                    visible: {{ App\Helpers\Helper::checkRole('customers', 'standard_flat_number') }},
                    className: visibility["{{ App\Helpers\Helper::checkRole('customers', 'standard_flat_number') }}"],
                },
                {
                    data: 'city',
                    name: 'city',
                    visible: {{ App\Helpers\Helper::checkRole('customers', 'standard_city') }},
                    className: visibility["{{ App\Helpers\Helper::checkRole('customers', 'standard_city') }}"],
                },
                {
                    data: 'postal_code',
                    name: 'postal_code',
                    visible: {{ App\Helpers\Helper::checkRole('customers', 'standard_postal_code') }},
                    className: visibility["{{ App\Helpers\Helper::checkRole('customers', 'standard_postal_code') }}"],
                },
                {
                    data: 'email',
                    name: 'email',
                    visible: {{ App\Helpers\Helper::checkRole('customers', 'standard_email') }},
                    className: visibility["{{ App\Helpers\Helper::checkRole('customers', 'standard_email') }}"],
                },
                {
                    data: 'status',
                    name: 'status',
                    className: 'column-status dt-center',
                    render: function (status) {
                        if (status === 'ACTIVE') {
                            return '<span style="color: green;">' + {!! json_encode(__('customers.table.active'), true) !!} +'</span>';
                        } else {
                            return '<span style="color: red;">' + {!! json_encode(__('customers.table.pending'), true) !!} +'</span>';
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
                    render: function (id, type, row) {
                        console.log(row);
                        let html = '<a href="{{ url()->current() }}/' + row['customer_id'] + '/list" class="btn btn-sm btn-primary edit">';
                        html += '<i class="voyager-edit"></i>';
                        html += '<span class="hidden-xs hidden-sm">Płatności</span>';
                        html += '</a>';

                        return html;
                    }
                }
            ]
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
        table.button().add({{1+$key}},{
            extend: 'colvisGroup',
            text: '{{$row->display_name}}',
            show: {{'show'.$row->name}},
            hide: {{'hidden'.$row->name}}
        });
        @endforeach

        $('#dataTable thead tr th').each(function (i) {
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
        $('#dataTable').on( 'column-visibility.dt', function ( e, settings, column, state ) {
            console.log([column, state]);
            if(state === true) {
                    let columnSearch =  $("#columnSearch" + (column));
                    columnSearch.parent().show();

            } else {
                    let columnSearch =  $("#columnSearch" + (column));
                    columnSearch.parent().hide();
            }

        });
    </script>
    <style>
        #dataTable thead tr:first-child th:first-child {
            width: 60px !important;
        }
    </style>
@endsection
