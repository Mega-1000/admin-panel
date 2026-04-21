@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-person"></i> @lang('column_visibilities.roles.title') {{$moduleName}}

    </h1>
@endsection

@section('table')
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th>@lang('column_visibilities.roles.name')</th>
            <th>@lang('column_visibilities.roles.display_name')</th>

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
            breadcrumb.append("<li class='active'><a href='/admin/columnVisibilities/modules'><i class='voyager-boat'></i>Moduły</a></li>");
            breadcrumb.append("<li class='active'><a href='javascript:void();'>Role</a></li>");

            // DataTable
            let table = $('#dataTable').DataTable({
                language: {!! json_encode( __('voyager.datatable'), true) !!},
                processing: true,
                serverSide: true,
                columnDefs: [
                    {className: "dt-center", targets: "_all"}
                ],
                order: [[0, "asc"]],
                ajax: '{!! route('columnVisibilities.modules.roles.datatable',['module_id'=>$module_id]) !!}',
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
                      data:'display_name',
                      name:'display_name'
                    },

                    {
                        data: 'id',
                        name: 'id',
                        render: function (id) {
                            let html = '{{ method_field('put') }}';
                            html += '{{ csrf_field() }}';
                            html += '<a href="{{url()->current() }}/' + id + '/visibilities" class="btn btn-sm btn-primary edit">';
                            html += '<i class="voyager-edit"></i>';
                            html += '<span class="hidden-xs hidden-sm"> @lang('column_visibilities.roles.visibilities')</span>';
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



        });
    </script>
@endsection
