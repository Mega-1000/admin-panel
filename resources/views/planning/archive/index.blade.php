@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-archive"></i> @lang('archive.title')
    </h1>
@endsection

@section('table')
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th>@lang('archive.table.name')</th>
            <th>@lang('archive.table.user_id')</th>
            <th>@lang('archive.table.order_id')</th>
            <th>@lang('archive.table.warehouse_id')</th>
            <th>@lang('archive.table.created_by')</th>
            <th>@lang('archive.table.date_start')</th>
            <th>@lang('archive.table.date_end')</th>
            <th>@lang('archive.table.created_at')</th>
            <th>@lang('voyager.generic.actions')</th>
        </tr>
        </thead>
    </table>
@endsection


@section('datatable-scripts')
    <script>
        var breadcrumb = $('.breadcrumb:nth-child(2)');

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/planning/timetable'>Planowanie pracy</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/planning/archive'>Archiwum</a></li>");

        // DataTable
        let table = $('#dataTable').DataTable({
            language: {!! json_encode( __('voyager.datatable'), true) !!},
            processing: true,
            serverSide: true,
            columnDefs: [
                { className: "dt-center", targets: "_all" }
            ],
            dom: 'Bfrtip',
            buttons: [
                {extend: 'colvis', text : 'Widzialność kolumn'}
            ],
            order: [[0, "asc"]],
            ajax: '{!! route('planning.archive.datatable') !!}',
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'user.name',
                    name: 'user_id',
                },
                {
                    data: 'order_id',
                    name: 'order_id',
                },
                {
                    data: 'warehouse.symbol',
                    name: 'warehouse_id',
                },
                {
                    data: 'created_by',
                    name: 'created_by',
                },
                {
                    data: 'task_time.date_start',
                    name: 'date_start',
                },
                {
                    data: 'task_time.date_end',
                    name: 'date_end',
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function (id) {
                        let html = '<a href="{{ url()->current() }}/' + id + '/view" class="btn btn-sm btn-warning view">';
                        html += '<i class="voyager-eye"></i>';
                        html += '<span class="hidden-xs hidden-sm">Zobacz</span>';
                        html += '</a>';

                        return html;
                    }
                }
            ]
        });
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
            console.log(column);
            if(state == true) {
                $("#columnSearch" + column).parent().show();
            } else {
                $("#columnSearch" + column).parent().hide();
            }

        });
        $('#dataTable > thead > tr:nth-child(2) > th:nth-child(8)')[0].innerText = '';

    </script>
@endsection
