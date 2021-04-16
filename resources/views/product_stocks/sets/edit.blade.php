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
            <div class="form-group">
                <label for="stock">@lang('sets.form.stock')</label>
                <input type="text" class="form-control" id="stock" name="stock" value="{{ $set->stock }}">
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
    <form action="{{ action('SetsController@addProduct', ['set' => $set->id]) }}" method="POST">
        {{ csrf_field() }}
        <div class="product_stocks-general" id="general">
            <div class="form-group">
                <label for="name">@lang('sets.form.add_product_header')</label>
                <select class="form-control text-uppercase" name="product_id">
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->symbol }}  => {{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="stock">@lang('sets.form.packet_product_quantity')</label>
                <input type="number" class="form-control" id="stock" name="stock" min="1" value="1">
            </div>
        </div>
        <button type="submit" id="store__packet" class="btn btn-primary">@lang('sets.form.add_product')</button>
    </form>
@endsection
