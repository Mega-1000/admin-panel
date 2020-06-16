@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> Szablony
        <a href="{!! route('package_templates.create') !!}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>Dodaj nowy szablon</span>
        </a>
    </h1>
@endsection

@section('table')
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>@lang('order_packages.form.list_order')</th>
            <th>@lang('order_packages.form.template_symbol')</th>
            <th>@lang('order_packages.form.deliverer')</th>
            <th>@lang('order_packages.form.delivery')</th>
            <th>@lang('order_packages.form.service_courier_name')</th>
            <th>@lang('order_packages.form.delivery_courier_name')</th>
            <th>@lang('order_packages.form.container_type')</th>
            <th>@lang('order_packages.form.packing_type')</th>
            <th>Nazwa</th>
            <th>Data utworzenia</th>
            <th>@lang('voyager.generic.actions')</th>
        </tr>
        </thead>
        <tbody>
         @foreach ($packageTemplates as $packageTemplate)
        <tr>
            <td>{{$packageTemplate->id}}</td>
            <td>@if($packageTemplate->list_order!=1000){{$packageTemplate->list_order}}@endif</td>
            <td>{{$packageTemplate->symbol}}</td>
            <td>{{$packageTemplate->sello_deliverer_id}}</td>
            <td>{{$packageTemplate->sello_delivery_id}}</td>
            <td>{{$packageTemplate->service_courier_name}}</td>
            <td>{{$packageTemplate->delivery_courier_name}}</td>
            <td>{{$packageTemplate->container_type}}</td>
            <td>{{$packageTemplate->packing_type}}</td>
            <td>{{$packageTemplate->name}}</td>
            <td>{{$packageTemplate->created_at}}</td>
            <td>
                <div class="col-md-10">
                    <a href="{{ url()->current() }}/{{$packageTemplate->id}}/edit" class="btn btn-sm btn-primary edit">
                        <i class="voyager-edit"></i>
                        <span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>
                    </a>
                    <form action="{{ action('PackageTemplatesController@destroy', $packageTemplate->id) }}" method="POST" >
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
