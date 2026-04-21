@extends('layouts.datatable')

@section('app-header')
    <link href="/css/views/pages/treeview.css" rel="stylesheet">

    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('pages.add_content')
        <a href="{!! route('pages.newContent', ['id' => $page->id]) !!}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>@lang('pages.add_content')</span>
        </a>
    </h1>
@endsection

@section('table')
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>@lang('pages.table.name')</th>
            <th>@lang('pages.table.created')</th>
            <th>@lang('pages.table.edited')</th>
            <th>@lang('voyager.generic.actions')</th>
        </tr>
        @foreach($page->pages as $site)
            <tr>
                <th>{{$site->id}}</th>
                <th>{{$site->title}}</th>
                <th>{{$site->created_at}}</th>
                <th>{{$site->updated_at}}</th>
                <th>
                    <button type="button" class="btn btn-danger" style="margin-left: 12px" onclick="window.location='{{ route('pages.deleteContent', ['content_id' => $site->id]) }}'">
                        @lang('voyager.generic.delete')
                    </button>
                    <button type="button" class="btn btn-primary" onclick="window.location='{{ route('pages.editContent', ['id' => $page->id, 'content_id' => $site->id]) }}'">
                        @lang('voyager.generic.edit')
                    </button>
                </th>
            </tr>
        @endforeach
        </thead>
    </table>
@endsection
