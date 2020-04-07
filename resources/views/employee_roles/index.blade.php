@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('firms.roles')
        <a href="{!! route('employee_role.create') !!}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>@lang('firms.role_create')</span>
        </a>
    </h1>
@endsection
@section('table')
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>@lang('firms.form.symbol')</th>
            <th>@lang('firms.form.role_name')</th>
            <th>@lang('firms.form.role_displayed')</th>
            <th>Data utworzenia</th>
            <th>@lang('voyager.generic.actions')</th>
        </tr>
        </thead>
        <tbody>
         @foreach ($roles as $role)
        <tr>
            <td width="10%">{{$role->id}}</td>
            <td width="10%">{{$role->symbol}}</td>
            <td width="30%">{{$role->name}}</td>
            <td width="10%">{{$role->is_contact_displayed_in_fronted  ? 'tak' : 'nie'}}</td>
            <td width="20%">{{$role->created_at}}</td>
            <td>
                <div class="col-md-10">
                    <a href="{{ url()->current() }}/{{$role->id}}/edit" class="btn btn-sm btn-primary edit">
                        <i class="voyager-edit"></i>
                        <span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>
                    </a>
                    <form action="{{ action('EmployeeRoleController@destroy', $role->id) }}" method="POST" >
                        {{ method_field('DELETE')}}
                        {{ csrf_field() }}
                        <button type="submit"  class="btn btn-sm btn-danger edit">
                            <i class="voyager-edit"></i>
                            <span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
         @endforeach
        </tbody>
    </table>


@endsection

@section('datatable-scripts')

@endsection
