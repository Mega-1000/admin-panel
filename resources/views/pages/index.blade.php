@extends('layouts.datatable')

@section('app-header')
    <link href="/css/views/pages/treeview.css" rel="stylesheet">

    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('pages.title')
        <a href="{!! route('pages.create') !!}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>@lang('pages.create')</span>
        </a>
    </h1>
@endsection

@section('table')
    <ul id="tree">
        @foreach($pages as $page)
            <li>
                {{ $page->name }}
                @if(count($page->childs))
                    @include('pages.manageChild',['childs' => $page->childs])
                @endif
            </li>
        @endforeach
    </ul>
@endsection

@section('datatable-scripts')
    <script>
        $.fn.extend({
            treed: function () {
                var openedClass = 'voyager-angle-down';
                var closedClass = 'voyager-angle-up';

                var tree = $(this);
                tree.addClass("tree");
                tree.find('li').has("ul").each(function () {
                    var branch = $(this);
                    branch.prepend("");
                    branch.addClass('branch icon');
                    if (branch.has()) {
                        branch.addClass(closedClass)
                    }
                    branch.on('click', function (e) {
                        if (this == e.target) {
                            var icon = $(e.target)
                            icon.toggleClass(openedClass + " " + closedClass);
                            $(this).children().children().toggle();
                        }
                    })
                    branch.children().children().toggle();
                });
                /* fire event from the dynamically added icon */
                tree.find('.branch .indicator').each(function(){
                    $(this).on('click', function () {
                        $(this).closest('li').click();
                    });
                });
                /* fire event to open branch if the li contains an anchor instead of text */
                tree.find('.branch>a').each(function () {
                    $(this).on('click', function (e) {
                        $(this).closest('li').click();
                        e.preventDefault();
                    });
                });
                /* fire event to open branch if the li contains a button instead of text */
                tree.find('.branch>button').each(function () {
                    $(this).on('click', function (e) {
                        $(this).closest('li').click();
                        e.preventDefault();
                    });
                });
            }
        });
        /* Initialization of treeviews */
        $('#tree').treed();
    </script>
@endsection
