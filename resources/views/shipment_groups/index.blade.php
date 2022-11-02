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
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Kurier</th>
            <th>Typ grupy paczek</th>
            <th>Lp</th>
            <th>Data wysyłki</th>
            <th>Zamknięta</th>
            <th>Wysłana</th>
            <th>Akcje</th>
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
        breadcrumb.append("<li class='active'><a href='javascript:void();'>Statusy</a></li>");

        // DataTable
        let table = $('#dataTable').DataTable({
            language: {!! json_encode( __('voyager.datatable'), true) !!},
            processing: true,
            serverSide: true,
            columnDefs: [
                {className: "dt-center", targets: "_all"}
            ],
            dom: 'Bfrtip',
            buttons: [
                {extend: 'colvis', text: 'Widzialność kolumn'}
            ],
            order: [[0, "desc"]],
            ajax: '{!! route('shipment-groups.datatable') !!}',
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'courier_name',
                    name: 'courier_name'
                },
                {
                    data: 'package_type',
                    name: 'package_type'
                },
                {
                    data: 'lp',
                    name: 'lp'
                },
                {
                    data: 'shipment_date',
                    name: 'shipment_date'
                },
                {
                    data: 'closed',
                    name: 'closed',
                    render: function (closed) {
                        if (closed === '1') {
                            return '<span style="color: green;">Zamknięte</span>';
                        } else {
                            return '<span style="color: red;">Nie zamknięte</span>';
                        }
                    }
                },
                {
                    data: 'sent',
                    name: 'sent',
                    render: function (sent) {
                        if (sent === '1') {
                            return '<span style="color: green;">Wysłane</span>';
                        } else {
                            return '<span style="color: red;">Nie wysłane</span>';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function (id) {
                        let html = '';
                        html += '<a href="{{ url()->current() }}/' + id + '/show" class="btn btn-sm btn-primary edit">';
                        html += '<i class="voyager-home"></i>';
                        html += '<span class="hidden-xs hidden-sm">Szczegóły</span>';
                        html += '</a>';

                        html += '<a href="{{ url()->current() }}/' + id + '/edit" class="btn btn-sm btn-primary edit">';
                        html += '<i class="voyager-edit"></i>';
                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>';
                        html += '</a>';

                        html += '<button class="btn btn-sm btn-danger delete delete-record" onclick="deleteRecord(' + id + ')">';
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


        $('#dataTable').on('column-visibility.dt', function (e, settings, column, state) {
            if (state == true) {
                $("#columnSearch" + column).parent().show();
            } else {
                $("#columnSearch" + column).parent().hide();
            }

        });
        // $('#dataTable > thead > tr:nth-child(2) > th:nth-child(8)')[0].innerText = '';

    </script>
@endsection
