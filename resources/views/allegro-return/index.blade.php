@extends("layouts.datatable")

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-window-list"></i> Zwróć płatność allegro
    </h1>
@endsection

@section('table')
    @if($order->items)
        <form enctype="multipart/form-data" action="{{ action('AllegroReturnPaymentController@store', ['orderId' => $order->id])}}" method="POST" class="form-horizontal">
            {{ csrf_field() }}
            <div class="grid">
                @if(count($existingAllegroReturns) > 0)
                    <div class="alert alert-warning">
                        <strong>Uwaga!</strong> Istnieją już zwroty dla tego zamówienia.
                    </div>
                @endif
                @foreach($order->items as $item)
                    <div style="width: 60%">
                        <h4>
                            <img src="{!! $item->product->getImageUrl() !!}" style="width: 179px; height: 130px;">
                            <strong>{{ $loop->iteration }}. </strong>{{ $item->product->name }}
                            (symbol: {{ $item->product->symbol }})
                        </h4>
                        <hr />
                    </div>
                    <div style="width: 40%">
                        <input type="hidden" name="return[{{$loop->iteration}}][id]"
                                               @if(count($item->realProductPositions())) @if(isset($order->returnPosition($item->realProductPositions()->first()['id'])->id))value="{{$order->returnPosition($item->realProductPositions()->first()['id'])->id}}" @endif @endif>
                        <input class="return-check" type="checkbox"
                                name="return[{{$loop->iteration}}][check]" value="{{$loop->iteration}}"
                                @if(count($item->realProductPositions()) && $order->returnPosition($item->realProductPositions()[0]['id'])!==null) checked @endif>
                        Dodaj zwrot
                    </div>
                @endforeach
            </div>
        </form>
    @endif
@endsection
