@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-person"></i> @lang('users.title')
        <a href="{!! route('users.create') !!}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>@lang('users.create')</span>
        </a>
    </h1>
@endsection

@section('table')
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th>@lang('users.table.name')</th>
            <th>@lang('users.table.firstname')</th>
            <th>@lang('users.table.lastname')</th>
            <th>@lang('users.table.role')</th>
            <th>@lang('users.table.phone')</th>
            <th>@lang('users.table.phone2')</th>
            <th>@lang('users.table.email')</th>
            <th>@lang('users.table.status')</th>
            <th>@lang('users.table.created_at')</th>
            <th>@lang('voyager.generic.actions')</th>
        </tr>
        </thead>
    </table>
@endsection


@section('datatable-scripts')
    <script>
        const deleteRecord = (id) => {
            $('#delete_form')[0].action = "{{ url()->current() }}-destroy/" + id;
            $('#delete_modal').modal('show');
        };
        $(document).ready(function() {

            var breadcrumb = $('.breadcrumb:nth-child(2)');

            breadcrumb.children().remove();
            breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
            breadcrumb.append("<li class='active'><a href='javascript:void();'>Użytkownicy</a></li>");

            // DataTable
            let table = $('#dataTable').DataTable({
                language: {!! json_encode( __('voyager.datatable'), true) !!},
                processing: true,
                serverSide: true,
                columnDefs: [
                    {className: "dt-center", targets: "_all"}
                ],
                order: [[0, "asc"]],
                ajax: '{!! route('users.datatable') !!}',
                dom: 'Bfrtip',
                buttons: [
                    {extend: 'colvis', text: 'Widzialność kolumn'}
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'firstname',
                        name: 'firstname'
                    },
                    {
                        data: 'lastname',
                        name: 'lastname'
                    },
                    {
                        data: 'role_id',
                        name: 'role_id',
                        render: function (role_id) {
                            if (role_id == 1) {
                                return '<span>SUPER ADMIN</span>';
                            } else if(role_id == 2) {
                                return '<span>ADMINISTRATOR</span>';
                            } else if(role_id == 3) {
                                return '<span>KSIĘGOWY</span>';
                            } else if(role_id == 4) {
                                return '<span>KONSULTANT</span>';
                            } else if(role_id == 5) {
                                return '<span>MAGAZYNIER</span>';
                            }
                        }
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'phone2',
                        name: 'phone2'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function (status) {
                            if (status === 'ACTIVE') {
                                return '<span style="color: green;">' + {!! json_encode(__('users.table.active'), true) !!} +'</span>';
                            } else {
                                return '<span style="color: red;">' + {!! json_encode(__('users.table.pending'), true) !!} +'</span>';
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
                        render: function (id) {
                            let user = '{{Auth::user()->id}}';
                            let role = '{{Auth::user()->role_id}}';
                            let html = '<form action="{{ url()->current() }}/' + id + '/change-status" method="POST" style="display: inline;">';
                            html += '{{ method_field('put') }}';
                            html += '{{ csrf_field() }}';
                            if(id === user || role <= 2) {
                                    html += '<button type="submit" href="{{ url()->current() }}/' + id + '/change-status" class="btn btn-sm btn-primary delete">';
                                    html += '<span class="hidden-xs hidden-sm"> @lang('users.table.changeStatus')</span>';
                                    html += '<i class="voyager-code hidden-md hidden-lg"></i>';
                                    html += '</button>';
                                    html += '</form>';
                                    html += '<a href="{{ url()->current() }}/' + id + '/editItem" class="btn btn-sm btn-primary edit">';
                                    html += '<i class="voyager-edit"></i>';
                                    html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>';
                                    html += '</a>';
                                }
                                if(id !== user && role <= 2) {
                                        html += '<button class="btn btn-sm btn-danger delete delete-record" onclick="deleteRecord(' + id + ')">';
                                        html += '<i class="voyager-trash"></i>';
                                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>';
                                        html += '</button>';
                                    }
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


            $('#dataTable').on('column-visibility.dt', function (e, settings, column, state) {
                console.log(column);
                if (state == true) {
                    $("#columnSearch" + column).parent().show();
                } else {
                    $("#columnSearch" + column).parent().hide();
                }

            });
        });
    </script>
@endsection
