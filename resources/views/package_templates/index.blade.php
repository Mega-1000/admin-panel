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
            <th></th>
            <th>ID</th>
            <th>Nazwa</th>
            <th>Data utworzenia</th>
            <th>@lang('voyager.generic.actions')</th>
        </tr>
        </thead>
        <tbody>
         @foreach ($packageTemplates as $packageTemplate) 
        <td>{{$packageTemplates->id}}</td>
        <td>{{$packageTemplates->name}}</td>
        <td>{{$packageTemplates->created_at}}</td>
        <td>
            <a href="{{ url()->current() }}/{{$packageTemplates->id}}/edit" class="btn btn-sm btn-primary edit">
                <i class="voyager-edit"></i>
                <span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>
            </a>
            <a href="{{ url()->current() }}/{{$packageTemplates->id}}/delete" class="btn btn-sm btn-primary edit">
                <i class="voyager-edit"></i>
                <span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>
            </a>
        </td>
         @endforeach
        </tbody>
    </table>


@endsection

@section('datatable-scripts')
    
@endsection
