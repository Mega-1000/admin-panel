@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stock_packets.packet_list')
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
    <a class="btn btn-success" id="create__button" href="{{ route('product_stock_packets.create') }}">@lang('product_stock_packets.form.buttons.create')</a>
    <table class="table">
        <thead>
            <tr>
                <th>@lang('product_stock_packets.table.id')</th>
                <th>@lang('product_stock_packets.table.name')</th>
                <th>@lang('product_stock_packets.table.packet_quantity')</th>
                <th>@lang('product_stock_packets.table.packet_products')</th>
                <th>@lang('product_stock_packets.table.actions')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productStockPackets as $packet)
                <tr>
                    <td>
                        {{ $packet->id }}
                    </td>
                    <td>
                        {{ $packet->packet_name }}
                    </td>
                    <td>
                        {{ $packet->packet_quantity }}
                    </td>
                    <td>
                        @foreach($packet->items as $item)
                            <span>{{ $item->product->name }} - {{ $item->quantity }} sztuk</span><br>
                        @endforeach
                    </td>
                    <td>
                        <a class="btn btn-sm btn-primary" href="{{ route('product_stock_packets.edit', ['packetId' => $packet->id]) }}">
                            <i class="voyager-trash"></i>
                            <span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteRecord('{{$packet->id}}')">
                           <i class="voyager-trash"></i>
                           <span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>
                        </button>
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
