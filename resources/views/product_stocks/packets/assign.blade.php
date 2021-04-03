@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stock_packets.edit')
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
    <form id="splitOrders" action="{{ action('OrdersController@splitOrders')}}"
          method="post">
        {{ csrf_field() }}
        <table id="productsTable" class="table table1 table-venice-blue"
               style="width: 100%;">
            <tbody id="products-tbody">
            <tr>
                <td colspan="4">
                    Produkt w zamówieniu
                </td>
                <td>
                    Ilość do przydzielenia z pakietu
                </td>
                <td>
                    Różnica w produkcie
                </td>
            </tr>
            @foreach($order->items as $item)
                <tr class="id row-{{$item->id}}" id="id[{{$item->id}}]">
                    <td colspan="4"><h4><img
                                src="{!! $item->product->getImageUrl() !!}"
                                style="width: 179px; height: 130px;"><strong>{{ $loop->iteration }}
                                . </strong>{{ $item->product->name }}
                            (symbol: {{ $item->product->symbol }}) </h4>
                            <h4>Ilość produktu: <span id="product__quantity--{{$item->id}}">{{ $item->quantity }}</span></h4>
                    </td>
                    <td>
                        @php
                            $foundItem = $packet->items->filter(function($packetItem) use ($item) {
                                return $packetItem->product_id == $item->product->id;
                            })->first();
                        if(isset($foundItem)) $productQuantityInPacket = $foundItem->quantity / $foundItem->productStockPacket->packet_quantity;
                        @endphp
                        @if(isset($foundItem))
                            <input type="number" value="{{ $productQuantityInPacket }}" name="packetItem-{{ $foundItem->id }}">
                        @endif
                    </td>
                    <td>
                        @if(isset($foundItem))
                            @if($productQuantityInPacket - $item->quantity > 0)
                                <span style="color: green;">+{{ $productQuantityInPacket - $item->quantity }}</span>
                            @else
                                <span style="color: red;">-{{ $productQuantityInPacket - $item->quantity }}</span>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </form>
@endsection
@include('product_stocks.packets.modals.formModals')
@include('product_stocks.packets.includes.scripts')
