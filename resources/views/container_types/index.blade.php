@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_packages.containers')
        <a href="{!! route('container_type.create') !!}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>@lang('order_packages.container_create')</span>
        </a>
    </h1>
@endsection
@section('table')
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>@lang('order_packages.form.template_symbol')</th>
            <th>@lang('order_packages.form.container_type_name')</th>
            <th>Data utworzenia</th>
            <th>@lang('voyager.generic.actions')</th>
        </tr>
        </thead>
        <tbody>
         @foreach ($containerTypes as $containerType)
        <tr>
            <td width="10%">{{$containerType->id}}</td>
            <td width="15%">{{$containerType->symbol}}</td>
            <td width="35%">{{$containerType->name}}</td>
            <td width="20%">{{$containerType->created_at}}</td>
            <td>
                <div class="col-md-10">
                    <a href="{{ url()->current() }}/{{$containerType->id}}/edit" class="btn btn-sm btn-primary edit">
                        <i class="voyager-edit"></i>
                        <span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>
                    </a>
                    <form action="{{ action('ContainerTypesController@destroy', $containerType->id) }}" method="POST" >
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
