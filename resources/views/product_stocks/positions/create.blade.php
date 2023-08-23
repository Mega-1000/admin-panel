@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-eye"></i> @lang('product_stock_positions.create')
        <a style="margin-left: 15px;" href="{{ action('ProductStocksController@edit', ['id' => $id]) }}"
           class="btn btn-info install pull-right">
            <span>@lang('product_stock_positions.list')</span>
        </a>
    </h1>
    <script src="https://cdn.tailwindcss.com"></script>

    @livewireStyles
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

    <livewire:add-new-product-stock-position-form :productStockId="$id"/>
@endsection
@section('scripts')
    @livewireScripts
    <script>
        const breadcrumb = $('.breadcrumb:nth-child(2)');

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/product/stocks'>Stany magazynowe</a></li>");
        breadcrumb.append("<li class='disable'><a href='/admin/product/stocks/{{$id}}/edit'>Edytuj</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/product/stocks/{{$id}}/edit#positions'>Pozycje magazynowe</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Dodaj</a></li>");
    </script>
@endsection
