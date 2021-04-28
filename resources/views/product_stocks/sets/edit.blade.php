@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('sets.edit')
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
    <form action="{{ action('SetsController@update', ['set' => $set->id]) }}" method="POST">
        {{ csrf_field() }}
        <div class="product_stocks-general" id="general">
            <div class="form-group">
                <label for="name">@lang('sets.form.packet_name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $set->name }}">
            </div>
            <div class="form-group">
                <label for="number">@lang('sets.form.number')</label>
                <input type="text" class="form-control" id="number" name="number" value="{{ $set->number }}">
            </div>
        </div>
        <button type="submit" id="store__packet" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
    @if($set->products())
        <table class="table">
            <thead>
            <tr>
                <th>@lang('sets.table.id')</th>
                <th>@lang('sets.table.name')</th>
                <th>@lang('sets.form.packet_product_quantity')</th>
                <th>@lang('sets.table.actions')</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($set->products() as $product)
                <tr>
                    <td>
                        {{ $product->id }}
                    </td>
                    <td>
                        {{ $product->symbol }} => {{ $product->name }}
                    </td>
                    <td>
                        <form action="{{ action('SetsController@editProduct', ['productSet' => $product->id, 'set' => $set->id]) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <input type="number" min="1" class="form-control" id="stock" name="stock" value="{{ $product->stock }}">
                            </div>
                            <button type="submit" id="store__packet" class="btn btn-primary">@lang('sets.form.edit_stock')</button>
                        </form>
                    </td>
                    <td>
                        <form action="{{ action('SetsController@deleteProduct', ['productSet' => $product->id, 'set' => $set->id]) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-danger" type="submit">
                                <i class="voyager-trash"></i>
                                <span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    @endif
    <div class="form-group">
        <label for="search">Wyszukaj produkct aby dodać do zestawu</label>
        <input type="text" class="form-control" id="search">
    </div>
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th>@lang('product_stocks.table.name')</th>
            <th>@lang('product_stocks.table.symbol')</th>
            <th>@lang('voyager.generic.actions')</th>
        </tr>
        </thead>
        <tbody id="productTable">
        </tbody>
    </table>
@endsection
@section('datatable-scripts')
    <script>
        const existProducts = [
            @foreach ($set->products() as $product)
                {{ $product->product_id }},
            @endforeach
        ];
        const products = [
                @foreach ($products as $product)
            {
                "id": "{{ $product->id }}",
                "name": "{{ $product->name }}",
                "symbol": "{{ $product->symbol }}"
            },
            @endforeach
        ];
        const searchInput = document.querySelector('#search');
        const resultTable = document.querySelector('#productTable');
        searchInput.addEventListener('change', (event) => {
            createTable(searchProducts(searchInput.value));
        });
        function searchProducts(word) {
            return products.filter((product) => {
                if (product.name.search(word) != -1) {
                    return true;
                } else if (product.symbol.search(word) != -1) {
                    return true;
                } else {
                    return false;
                }
            });
        }
        function createTable(products) {
            resultTable.innerHTML = "";
            products.forEach((product, index) => {
                resultTable.innerHTML = resultTable.innerHTML + createRow(product, index);
            })
        }
        function createRow(product, index) {
            const form =
                '<td>'+
                '<form action="{{ action('SetsController@addProduct', ['set' => $set->id]) }}" method="POST">'+
                '@csrf'+
                '<input type="hidden" name="product_id" value="'+product.id+'">'+
                '<div class="form-group">'+
                '<label for="stock">@lang('sets.form.packet_product_quantity')</label>'+
                '<input type="number" class="form-control" id="stock" name="stock" min="1" value="1">'+
                '</div>'+
                '<button type="submit" id="store__packet" class="btn btn-primary">@lang('sets.form.add_product')</button>'+
                '</form>'+
                '</td>';
            const message = '<td>@lang('sets.messages.exist_product')</td>';
            const action = (existProducts.includes(parseInt(product.id))) ? message : form;
            return '<tr>'+
                '<td></td><td>'+(index + 1)+'</td>' +
                '<td>'+product.name+'</td>' +
                '<td>'+product.symbol+'</td>' +
                action +
                '</tr>';
        }
    </script>
@endsection
