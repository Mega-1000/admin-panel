@extends('layouts.datatable')

@section('app-header')
<h1 class="page-title">
    <i class="voyager-company"></i> @lang('firms.title')
    <a href="{!! route('firms.create') !!}" class="btn btn-success btn-add-new">
        <i class="voyager-plus"></i> <span>@lang('firms.create')</span>
    </a>
    <a href="{!! route('employee_role.create') !!}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>@lang('firms.role_create')</span>
    </a>

    <a href="{!! route('employee_role.index') !!}" class="btn btn-primary " style="margin-left: 20px; margin-bottom: 9px">
             <span>@lang('firms.list_role')</span>
    </a>

</h1>
    
@endsection

@section('table')
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th>@lang('firms.table.name')</th>
            <th>@lang('firms.table.short_name')</th>
            <th>@lang('firms.table.symbol')</th>
            <th>@lang('firms.table.firm_type')</th>
            <th>@lang('firms.table.email')</th>
            <th>@lang('firms.table.secondary_email')</th>
            <th>@lang('firms.table.nip')</th>
            <th>@lang('firms.table.account_number')</th>
            <th>@lang('firms.table.status')</th>
            <th>@lang('firms.table.phone')</th>
            <th>@lang('firms.table.secondary_phone')</th>
            <th>@lang('firms.table.notices')</th>
            <th>@lang('firms.table.secondary_notices')</th>
            <th>@lang('firms.table.created_at')</th>
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

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='javascript:void();'>Firmy</a></li>");


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
            ajax: '{!! route('firms.datatable') !!}',
            orderCellsTop: true,
            fixedHeader: true,
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
                    data: 'short_name',
                    name: 'short_name'
                },
                {
                    data: 'symbol',
                    name: 'symbol'
                },
                {
                    data: 'firm_type',
                    name: 'firm_type',
                    render: function(data){
                        if (data === 'PRODUCTION') {
                            return '<span>' + {!! json_encode(__('firms.table.production'), true) !!} + '</span>';
                        } else if (data === 'DELIVERY') {
                            return '<span>' + {!! json_encode(__('firms.table.delivery'), true) !!} + '</span>';
                        } else if (data === 'OTHER') {
                            return '<span>' + {!! json_encode(__('firms.table.other'), true) !!} + '</span>';
                        }
                    }
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'secondary_email',
                    name: 'secondary_email'
                },
                {
                    data: 'nip',
                    name: 'nip'
                },
                {
                    data: 'account_number',
                    name: 'account_number'
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(status) {
                        if (status === 'ACTIVE') {
                            return '<span style="color: green;">' + {!! json_encode(__('firms.table.active'), true) !!} + '</span>';
                        } else {
                            return '<span style="color: red;">' + {!! json_encode(__('firms.table.pending'), true) !!} + '</span>';
                        }
                    }
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'secondary_phone',
                    name: 'secondary_phone'
                },
                {
                    data: 'notices',
                    name: 'notices'
                },
                {
                    data: 'secondary_notices',
                    name: 'secondary_notices'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function (id) {
                        let html = '<form action="{{ url()->current() }}/' + id + '/change-status" method="POST" style="display: inline;">';
                        html += '{{ method_field('put') }}';
                        html += '{{ csrf_field() }}';
                        html += '<button type="submit" href="{{ url()->current() }}/' + id + '/change-status" class="btn btn-sm btn-primary delete">';
                        html += '<span class="hidden-xs hidden-sm"> @lang('firms.table.changeStatus')</span>';
                        html += '<i class="voyager-code hidden-md hidden-lg"></i>';
                        html += '</button>';
                        html += '</form>';
                        html += '<a href="{{ url()->current() }}/' + id + '/sendRequestToUpdateFirmData" class="btn btn-success edit">';
                        html += '<span class="hidden-xs hidden-sm"> Wyślij prośbę o aktualizację danych</span>';
                        html += '</a>';
                        html += '<a href="{{ url()->current() }}/' + id + '/edit" class="btn btn-sm btn-primary edit">';
                        html += '<i class="voyager-edit"></i>';
                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>';
                        html += '</a>';

                        html += '<button class="btn btn-sm btn-danger delete delete-record" onclick="deleteRecord('+ id +')">';
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
            console.log(column);
            if(state == true) {
                $("#columnSearch" + column).parent().show();
            } else {
                $("#columnSearch" + column).parent().hide();
            }
            
        });
    </script>
@endsection
