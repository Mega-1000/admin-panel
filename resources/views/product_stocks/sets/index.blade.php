@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('sets.packet_list')
        <a style="margin-left: 15px;" href="{{ action('ProductStocksController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('product_stocks.list')</span>
        </a>
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
    <a class="btn btn-success" id="create__button" href="{{ route('sets.create') }}">@lang('sets.form.buttons.create')</a>
    <table class="table">
        <thead>
        <tr>
            <th>@lang('sets.table.id')</th>
            <th>@lang('sets.table.name')</th>
            <th>@lang('sets.table.number')</th>
            <th>@lang('sets.table.packet_quantity')</th>
            <th>@lang('sets.table.products')</th>
            <th>@lang('sets.table.completingSets')</th>
            <th>@lang('sets.table.disassemblySets')</th>
            <th>@lang('sets.table.actions')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($sets as $set)
            <tr>
                <td>
                    {{ $set->id }}
                </td>
                <td>
                    {{ $set->name }}
                </td>
                <td>
                    {{ $set->number }}
                </td>
                <td>
                    {{ $set->stock }}
                </td>
                <td>
                    <ul>
                        @foreach($set->products() as $product)
                            <li><b>{{ $product->symbol }}</b> => {{ $product->name }} <b>Ilość: {{ $product->stock }}</b></li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    <form action="{{ route('sets.completingSets', ['set' => $set->id]) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="number">@lang('sets.form.number_sets')</label>
                            <input type="number" class="form-control" id="number" name="number" min="1">
                        </div>
                        <button class="btn btn-sm btn-primary" type="submit">
                            <span class="hidden-xs hidden-sm"> @lang('sets.form.completingSets')</span>
                        </button>
                    </form>
                </td>
                <td>
                    <form action="{{ route('sets.disassemblySets', ['set' => $set->id]) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="number">@lang('sets.form.number_sets')</label>
                            <input type="number" class="form-control" id="number" name="number" min="1" max="{{ $set->stock }}">
                        </div>
                        <button class="btn btn-sm btn-primary" type="submit">
                            <span class="hidden-xs hidden-sm"> @lang('sets.form.disassemblySets')</span>
                        </button>
                    </form>
                </td>
                <td>
                    <a class="btn btn-sm btn-primary" href="{{ route('sets.edit', ['set' => $set->id]) }}">
                        <i class="voyager-trash"></i>
                        <span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>
                    </a>
                    <form action="{{ route('sets.delete', ['set' => $set->id]) }}" method="POST">
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
    <script>
        const deleteRecord = (id) => {
            $('#delete_form')[0].action = "{{ url()->current() }}/" + id;
            $('#delete_modal').modal('show');
        };
    </script>
@endsection
