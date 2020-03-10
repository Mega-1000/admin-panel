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
            <th>Nazwa</th>
            <th>Data utworzenia</th>
            <th>@lang('voyager.generic.actions')</th>
        </tr>
        </thead>
        <tbody>
         @foreach ($packageTemplates as $packageTemplate)
        <tr>
            <td width="10%">{{$packageTemplate->id}}</td>
            <td width="40%">{{$packageTemplate->name}}</td>
            <td width="30%">{{$packageTemplate->created_at}}</td>
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
