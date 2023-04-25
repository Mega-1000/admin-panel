@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-company"></i> @lang('firms.edit')
        <a style="margin-left: 15px;" href="{{ action('FirmsController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('firms.list')</span>
        </a>
    </h1>
@endsection


@section('table')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div style="margin-bottom: 15px;" class="tab">
        <button class="btn btn-primary active"
                name="change-button-form" id="button-general"
                value="general">@lang('firms.form.buttons.general')</button>
        <button class="btn btn-primary"
                name="change-button-form" id="button-address"
                value="address">@lang('firms.form.buttons.address')</button>
        <button class="btn btn-primary"
                name="change-button-form" id="button-warehouses"
                value="warehouses">@lang('firms.form.buttons.warehouses')</button>
        <button class="btn btn-primary"
                name="change-button-form" id="button-employees"
                value="employees">@lang('firms.form.buttons.employees')</button>
        <a id="create-button-warehouse" style="float:right;margin-right: 15px;"
           href="{{route('warehouses.create', ['firm_id' => $firm->id]) }}" class="btn btn-success install pull-right">
            <i class="voyager-plus"></i> <span>@lang('warehouses.create')</span>
        </a>
        <a id="create-button-employee" style="float:right;margin-right: 15px;"
           href="{{route('employees.create', ['firm_id' => $firm->id]) }}" class="btn btn-success install pull-right">
            <i class="voyager-plus"></i> <span>@lang('employees.create')</span>
        </a>
    </div>
    <form id="firms" action="{{ action('FirmsController@update', ['id' => $firm->id])}}"
          method="POST">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="firms-general" id="general">
            <input type="hidden" value="{{Session::get('uri')}}" id="uri">
            <div class="form-group">
                <label for="name">@lang('firms.form.name')</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="{{ $firm->name }}">
            </div>
            <div class="form-group">
                <label for="short_name">@lang('firms.form.short_name')</label>
                <input class="form-control" id="short_name"
                       name="short_name" maxlength="50"
                       value="{{ $firm->short_name }}">
            </div>
            <div class="form-group">
                <label for="symbol">@lang('firms.form.symbol')</label>
                <input class="form-control" id="symbol"
                       name="symbol"
                       value="{{ $firm->symbol }}">
            </div>
            <div class="form-group">
                <label for="firm_type">@lang('firms.form.firm_type')</label>
                <select class="form-control text-uppercase" name="firm_type">
                    <option {{ $firm->firm_type === 'PRODUCTION' ? 'selected="selected"' : ''}} value="PRODUCTION">@lang('firms.form.production')</option>
                    <option {{ $firm->firm_type === 'DELIVERY' ? 'selected="selected"' : ''}} value="DELIVERY">@lang('firms.form.delivery')</option>
                    <option {{ $firm->firm_type === 'OTHER' ? 'selected="selected"' : ''}} value="OTHER">@lang('firms.form.other')</option>
                </select>
            </div>
            <div class="form-group">
                <label for="delivery_warehouse">@lang('firms.form.delivery_warehouse')</label>
                <input type="text" class="form-control" id="delivery_warehouse" name="delivery_warehouse"
                       value="{{ $firm->delivery_warehouse }}">
            </div>
            <div class="form-group">
                <label for="firm_source">@lang('firms.form.firm_source')</label>

                <select class="form-control text-uppercase" name="firm_source[]" multiple>
                    @foreach ($orderSources as $orderSource)
                    <option {{$firm->firmSources->firstWhere('order_source_id', $orderSource->id) ? 'selected' : ''}} value="{{$orderSource->id}}">{{$orderSource->name}} ({{$orderSource->short_name}})</option>
                    @endforeach
                </select>
            </div>
            <a href="/admin/firms/{{$firm->id}}/sendRequestToUpdateFirmData" class="btn btn-success">
                <span class="hidden-xs hidden-sm">Wyślij prośbę o aktualizację danych</span>
            </a>
            <div class="form-group">
                <label for="email">@lang('firms.form.email')</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="{{ $firm->email }}">
            </div>
            <div class="form-group">
                <label for="secondary_email">@lang('firms.form.secondary_email')</label>
                <input type="email" class="form-control" id="secondary_email" name="secondary_email"
                       value="{{ $firm->secondary_email }}">
            </div>
            <div class="form-group">
                <label for="complaint_email">@lang('firms.form.complaint_email')</label>
                <input type="email" class="form-control" id="complaint_email" name="complaint_email"
                       value="{{ $firm->complaint_email }}">
            </div>
            <div class="form-group">
                <label for="nip">@lang('firms.form.nip')</label>
                <input type="text" class="form-control" id="nip" name="nip"
                       value="{{ $firm->nip }}">
            </div>
            <div class="form-group">
                <label for="account_number">@lang('firms.form.account_number')</label>
                <input type="text" class="form-control" id="account_number" name="account_number"
                       value="{{ $firm->account_number }}">
            </div>
            <div class="form-group">
                <label for="status">@lang('firms.form.status')</label>
                <select class="form-control text-uppercase" name="status">
                    <option value="ACTIVE">@lang('firms.form.active')</option>
                    <option value="PENDING">@lang('firms.form.pending')</option>
                </select>
            </div>
            <div class="form-group">
                <label for="phone">@lang('firms.form.phone')</label>
                <input type="text" class="form-control" id="phone" name="phone"
                       value="{{ $firm->phone }}">
            </div>
            <div class="form-group">
                <label for="secondary_phone">@lang('firms.form.secondary_phone')</label>
                <input type="text" class="form-control" id="secondary_phone" name="secondary_phone"
                       value="{{ $firm->secondary_phone }}">
            </div>
            <div class="form-group">
                <label for="notices">@lang('firms.form.notices')</label>
                <textarea cols="40" rows="5" type="text" class="form-control" id="notices" name="notices"
                       >{{ $firm->notices }}</textarea>
            </div>
            <div class="form-group">
                <label for="secondary_notices">@lang('firms.form.secondary_notices')</label>
                <textarea cols="40" rows="5" type="text" class="form-control" id="secondary_notices" name="secondary_notices"
                          >{{ $firm->secondary_notices }}</textarea>
            </div>
        </div>
        <div class="firms-address" id="address">
            <div class="form-group">
                <label for="postal_code">@lang('firms.form.address.postal_code')</label>
                <input type="text" class="form-control" id="postal_code" name="postal_code"
                       value="{{ $firmAddress->first->id->postal_code }}">
            </div>
            <div class="form-group">
                <label for="city">@lang('firms.form.address.city')</label>
                <input type="text" class="form-control" id="city" name="city"
                       value="{{ $firmAddress->first->id->city }}">
            </div>
            <div class="form-group">
                <label for="latitude">@lang('firms.form.address.latitude')</label>
                <input type="text" class="form-control" id="latitude" name="latitude"
                       value="{{ $firmAddress->first->id->latitude }}">
            </div>
            <div class="form-group">
                <label for="longitude">@lang('firms.form.address.longitude')</label>
                <input type="text" class="form-control" id="longitude" name="longitude"
                       value="{{ $firmAddress->first->id->longitude }}">
            </div>
            <div class="form-group">
                <label for="address">@lang('firms.form.address.address')</label>
                <input type="text" class="form-control" id="address" name="address"
                       value="{{ $firmAddress->first->id->address }}">
            </div>
            <div class="form-group">
                <label for="flat_number">@lang('firms.form.address.flat_number')</label>
                <input type="text" class="form-control" id="flat_number" name="flat_number"
                       value="{{ $firmAddress->first->id->flat_number }}">
            </div>
            <div class="form-group">
                <label for="address2">@lang('firms.form.address.address2')</label>
                <input type="text" class="form-control" id="address2" name="address2"
                       value="{{ $firmAddress->first->id->address2 }}">
            </div>
        </div>
    </form>
    <div class="firms-warehouses" id="warehouses">
        @if(!empty($uri))
            <input id="uri" type="hidden" value="{{$uri}}">
        @endif
        <table style="width: 100%" id="dataTableWarehouses" class="table table-hover">
            <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>@lang('warehouses.table.symbol')</th>
                <th>@lang('warehouses.table.warehouse_email')</th>
                <th>@lang('warehouses.table.address')</th>
                <th>@lang('warehouses.table.warehouse_number')</th>
                <th>@lang('warehouses.table.postal_code')</th>
                <th>@lang('warehouses.table.city')</th>
                <th>@lang('warehouses.table.status')</th>
                <th>@lang('warehouses.table.created_at')</th>
                <th>@lang('voyager.generic.actions')</th>
            </tr>
            </thead>
        </table>
    </div>
    <div class="firms-employees" id="employees">
        <table style="width: 100%" id="dataTableEmployees" class="table table-hover">
            <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>@lang('employees.table.firstname')</th>
                <th>@lang('employees.table.lastname')</th>
                <th>@lang('employees.table.phone')</th>
                <th>@lang('employees.table.email')</th>
                <th>@lang('employees.table.job_position')</th>
                <th>@lang('employees.table.comments')</th>
                <th>@lang('employees.table.additional_comments')</th>
                <th>@lang('employees.table.postal_code')</th>
                <th>@lang('employees.table.status')</th>
                <th>@lang('employees.table.created_at')</th>
                <th>@lang('voyager.generic.actions')</th>
            </tr>
            </thead>
            @foreach ($employees as $employee)
            <tbody>
                <tr>
                <td></td>
                <td>{{$employee->id}}</td>
                <td>{{$employee->firstname}}</td>
                <td>{{$employee->lastname}}</td>
                <td>{{$employee->phone}}</td>
                <td>{{$employee->email}}</td>
                <td>{{$employee->role}}</td>
                <td>{{$employee->comments}}</td>
                <td>{{$employee->additional_comments}}</td>
                <td>{{$employee->postal_code}}</td>
                <td>{{$employee->status}}</td>
                <td>{{$employee->created_at}}</td>
                <td>
                    <a href="/admin/employees/{{$employee->id}}/edit" class="btn btn-sm btn-primary edit">
                        <i class="voyager-edit"></i>
                        <span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>
                    </a>
                    <form action="{{ action('EmployeesController@destroy', $employee->id) }}" method="POST" >
                        {{ method_field('DELETE')}}
                        {{ csrf_field() }}
                        <button type="submit"  class="btn btn-sm btn-danger edit">
                            <i class="voyager-edit"></i>
                            <span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>
                        </button>
                </td>
                </tr>

            </tbody>
            @endforeach
        </table>
    </div>
    <button type="submit" form="firms"
            class="btn btn-primary">@lang('voyager.generic.save')</button>

@endsection
@section('datatable-scripts')
    <script>
        $(document).ready(function () {
            var general = $('#general').show();
            var address = $('#address').hide();
            var warehouses = $('#warehouses').hide();
            var employees = $('#employees').hide();
            var pageTitle = $('.page-title').children('i');
            var createButtonWarehouse = $('#create-button-warehouse').hide();
            var createButtonEmployee = $('#create-button-employee').hide();
            var uri = $('#uri').val();
            var value;
            var referrer = document.referrer;
            var breadcrumb = $('.breadcrumb');
            var item = '{{old('tab')}}';

            breadcrumb.children().remove();
            breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
            breadcrumb.append("<li class='active'><a href='/admin/firms/{{$firm->id}}/edit'>Firmy</a></li>");
            breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
            if (referrer.search('warehouses') != -1 || uri.search('warehouses') != -1 || item === 'warehouses') {
                $('#button-general').removeClass('active');
                $('#button-address').removeClass('active');
                $('#button-warehouses').addClass('active');
                $('#button-employees').removeClass('active');
                general.hide();
                address.hide();
                warehouses.show();
                employees.hide();
                createButtonWarehouse.show();
                createButtonEmployee.hide();
                pageTitle.removeClass();
                pageTitle.addClass('voyager-paint-bucket');
                breadcrumb.children().last().remove();
                breadcrumb.append("<li class='active'><a href='/admin/firms/{{$firm->id}}/edit#warehouses'>Magazyny</a></li>");

            } else if (referrer.search('employees') != -1 || uri.search('employees') != -1 || item === 'employees') {
                $('#button-general').removeClass('active');
                $('#button-address').removeClass('active');
                $('#button-warehouses').removeClass('active');
                $('#button-employees').addClass('active');
                general.hide();
                address.hide();
                warehouses.hide();
                employees.show();
                createButtonWarehouse.hide();
                createButtonEmployee.show();
                pageTitle.removeClass();
                pageTitle.addClass('voyager-people');

                breadcrumb.children().last().remove();
                breadcrumb.append("<li class='active'><a href='/admin/firms/{{$firm->id}}/edit#employees'>Pracownicy</a></li>");

            }
            $('[name="change-button-form"]').on('click', function () {
                value = this.value;
                $('#' + value).show();
                if (value === 'general') {
                    $('#button-general').addClass('active');
                    $('#button-address').removeClass('active');
                    $('#button-warehouses').removeClass('active');
                    $('#button-employees').removeClass('active');
                    address.hide();
                    warehouses.hide();
                    employees.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-company');
                    createButtonWarehouse.hide();
                    createButtonEmployee.hide();
                    breadcrumb.children().remove();
                    breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
                    breadcrumb.append("<li class='active'><a href='/admin/firms/{{$firm->id}}/edit'>Firmy</a></li>");
                    breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
                } else if (value === 'address') {
                    $('#button-general').removeClass('active');
                    $('#button-address').addClass('active');
                    $('#button-warehouses').removeClass('active');
                    $('#button-employees').removeClass('active');
                    general.hide();
                    warehouses.hide();
                    employees.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-home');
                    createButtonWarehouse.hide();
                    createButtonEmployee.hide();
                    breadcrumb.children().remove();
                    breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
                    breadcrumb.append("<li class='active'><a href='/admin/firms/{{$firm->id}}/edit'>Firmy</a></li>");
                    breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
                } else if (value === 'warehouses') {
                    $('#button-general').removeClass('active');
                    $('#button-address').removeClass('active');
                    $('#button-warehouses').addClass('active');
                    $('#button-employees').removeClass('active');
                    general.hide();
                    address.hide();
                    employees.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-paint-bucket');
                    createButtonWarehouse.show();
                    createButtonEmployee.hide();

                    breadcrumb.children().last().remove();
                    breadcrumb.append("<li class='active'><a href='/admin/firms/{{$firm->id}}/edit#warehouses'>Magazyny</a></li>");

                } else if (value === 'employees') {
                    $('#button-general').removeClass('active');
                    $('#button-address').removeClass('active');
                    $('#button-warehouses').removeClass('active');
                    $('#button-employees').addClass('active');
                    general.hide();
                    address.hide();
                    warehouses.hide();
                    pageTitle.removeClass();
                    pageTitle.addClass('voyager-people');
                    createButtonWarehouse.hide();
                    createButtonEmployee.show();
                    breadcrumb.children().last().remove();
                    breadcrumb.append("<li class='active'><a href='/admin/firms/{{$firm->id}}/edit#employees'>Pracownicy</a></li>");

                }
            });

        });

    </script>
    <script>
        const deleteRecordWarehouses = (id) =>{
            $('#delete_form')[0].action = "/admin/warehouses/" + id;
            $('#delete_modal').modal('show');
        };
        $.fn.dataTable.ext.errMode = 'throw';
        // DataTable
        let tableWarehouses = $('#dataTableWarehouses').DataTable({
            language: {!! json_encode( __('voyager.datatable'), true) !!},
            processing: true,
            serverSide: true,
            columnDefs: [
                {className: "dt-center", targets: "_all"}
            ],
            order: [[0, "asc"]],
            ajax: '{!! route('warehouses.datatable', ['id' => $firm->id]) !!}',
            dom: 'Bfrtip',
            buttons: [
                {extend: 'colvis', text : 'Widzialność kolumn'}
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
                    data: 'symbol',
                    name: 'symbol'
                },
                {
                    data: 'warehouse_email',
                    name: 'warehouse_email',
                    defaultContent: '',
                },
                {
                    data: 'address.address',
                    name: 'address.address',
                    defaultContent: ''
                },
                {
                    data: 'address.warehouse_number',
                    name: 'address.warehouse_number',
                    defaultContent: ''
                },
                {
                    data: 'address.postal_code',
                    name: 'address.postal_code',
                    defaultContent: ''
                },
                {
                    data: 'address.city',
                    name: 'address.city',
                    defaultContent: ''
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (status) {
                        if (status === 'ACTIVE') {
                            return '<span style="color: green;">' + {!! json_encode(__('employees.table.active'), true) !!} +'</span>';
                        } else {
                            return '<span style="color: red;">' + {!! json_encode(__('employees.table.pending'), true) !!} +'</span>';
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
                        let html = '<form action="/admin/warehouses/' + id + '/change-status" method="POST" style="display: inline;">';
                        html += '{{ method_field('put') }}';
                        html += '{{ csrf_field() }}';
                        html += '<button type="submit" href="/admin/warehouses/' + id + '/change-status" class="btn btn-sm btn-primary delete">';
                        html += '<span class="hidden-xs hidden-sm"> @lang('employees.table.change_status')</span>';
                        html += '</button>';
                        html += '</form>';

                        html += '<a href="/admin/warehouses/' + id + '/edit" class="btn btn-sm btn-primary edit">';
                        html += '<i class="voyager-edit"></i>';
                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>';
                        html += '</a>';

                        html += '<button class="btn btn-sm btn-danger delete delete-record" onclick="deleteRecordWarehouses(' + id + ')">';
                        html += '<i class="voyager-trash"></i>';
                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>';
                        html += '</button>';
                        return html;
                    }
                }
            ]
        });
        @foreach($visibilitiesWarehouse as $key =>$row)

        var {{'show'.$row->name}}  = @json($row->show);
        {{'show'.$row->name}} = {{'show'.$row->name}}.map(function(x){
            // if (typeof table.column(x+':name').index() === "number")
            return tableWarehouses.column(x+':name').index();
        });
        {{'show'.$row->name}} = {{'show'.$row->name}}.filter(function (el) {
            return el != null;
        });

        var {{'hidden'.$row->name}} = @json($row->hidden);
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.map(function(x){
            // if (typeof table.column(x+':name').index() === "number")
            return tableWarehouses.column(x+':name').index();
        });
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.filter(function (el) {
            return el != null;
        });
        tableWarehouses.button().add({{1+$key}},{
            extend: 'colvisGroup',
            text: '{{$row->display_name}}',
            show: {{'show'.$row->name}},
            hide: {{'hidden'.$row->name}}
        });
        @endforeach

        $('#dataTableWarehouses thead tr th').each(function (i) {
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

        $('#dataTableWarehouses > thead > tr:nth-child(2) > th:nth-child(10)')[0].innerText = '';
        </script>
@endsection
