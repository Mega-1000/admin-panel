@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-character"></i> @lang('labels.title')
        <a href="{!! route('labels.create') !!}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>@lang('labels.create')</span>
        </a>
    </h1>
@endsection

@section('table')
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th>
                <div><span>@lang('labels.table.order')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-order"/>
                </div>
            </th>
            <th>ID</th>
            <th>
                <div><span>@lang('labels.table.name')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-name"/>
                </div>
            </th>
            <th>
                <div><span>@lang('labels.table.group')</span></div>
                <div class="input_div">
                    <select class="columnSearchSelect" id="columnSearch-label_group_id">
                        <option value="">Wszystkie</option>
                        @foreach($labelGroups as $group)
                            <option value="{{$group->id}}">{{$group->name}}</option>
                        @endforeach
                    </select>
                </div>
            </th>
            <th>
                <div><span>@lang('labels.table.color')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-color"/>
                </div>
            </th>
            <th>@lang('labels.table.icon')</th>
            <th>@lang('labels.table.status')</th>
            <th>
                <div><span>@lang('labels.table.created_at')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-created_at"/>
                </div>
            </th>
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
        breadcrumb.append("<li class='active'><a href='javascript:void();'>Etykiety</a></li>");

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
            order: [[1, "asc"]],
            ajax: '{!! route('labels.datatable') !!}',
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    searchable: false,
                    orderable: false,
                    render: function (id) {
                        return '<input type="checkbox">';
                    }
                },
                {
                    data: 'order',
                    name: 'order'
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
                    data: 'label_group.name',
                    name: 'label_group_id',
                    defaultContent: '--brak--'
                },
                {
                    data: 'color',
                    name: 'color',
                    render: function(color, x, row){
                        return '<span style="background-color:'+ color +'; color: '+ row.font_color +';">'+color+'</span>';
                    }
                },
                {
                    data: 'icon_name',
                    name: 'icon_name',
                    render: function(icon, x, row){
                        return '<i style="font-size: 2.5rem; color: '+ row.color +'" class="' + icon + '"></i>';
                    }
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
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'id',
                    name: 'id',
                    searchable: false,
                    orderable: false,
                    render: function (id) {
                        let html = '<form action="{{ url()->current() }}/' + id + '/change-status" method="POST" style="display: inline;">';
                        html += '{{ method_field('put') }}';
                        html += '{{ csrf_field() }}';
                        html += '<button type="submit" href="{{ url()->current() }}/' + id + '/change-status" class="btn btn-sm btn-primary delete">';
                        html += '<span class="hidden-xs hidden-sm"> @lang('firms.table.changeStatus')</span>';
                        html += '</button>';
                        html += '</form>';

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
            $('input', this)
                .on('click', function (e) {
                    e.stopPropagation();
                })
                .on('keydown', function (e) {
                    if (e.keyCode == 13) {
                        e.preventDefault();
                        $(this).change();
                    }
                })
                .on('change', function (e) {
                    let colName = $(this)[0].id;
                    let colSelector = colName.substring(13, colName.length) + ":name";

                    if(table.column(colSelector).search() !== this.value) {
                        if(this.value == '') {
                            table
                                .column(colSelector)
                                .search('')
                                .draw();
                        } else {
                            table
                                .column(colSelector)
                                .search(this.value)
                                .draw();
                        }
                    }
                });
        });

        $("#columnSearch-label_group_id")
            .click(function (e) {
                e.stopPropagation();
            })
            .change(function () {
                if(table.column("label_group_id:name").search() !== this.value) {
                    if(this.value == '') {
                        table
                            .column("label_group_id:name")
                            .search('')
                            .draw();
                    } else {
                        table
                            .column("label_group_id:name")
                            .search(this.value)
                            .draw();
                    }
                }
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
