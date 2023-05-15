@extends('layouts.datatable')
@section('app-header')
    <link rel="stylesheet" href="{{ URL::asset('css/views/orders/edit.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/views/orders/return.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <h1 class="page-title" style="margin-right: 0px;">
        <i class="glyphicon glyphicon-share-alt"></i> Zwrot Towaru
        <a style="margin-left: 15px; height: 36px; margin-bottom: 8px; margin-top: 7px;"
           href="{{ action('OrdersController@index') }}"
           class="btn btn-info install pull-right">
            <span>Wróć do listy zamówień</span>
        </a>
    </h1>
    <button style="height: 36px; margin-bottom: 8px;" type="submit" form="ordersReturn" id="submitOrder" name="submit"
            value="update"
            class="btn btn-primary">Zapisz
    </button>
    <button style="height: 36px; margin-bottom: 8px;" type="submit" form="ordersReturn" id="submitOrderAndStay"
            name="submit"
            value="updateAndStay"
            class="btn btn-primary">Zapisz i pozostań
    </button>
@endsection

@section('table')
    <form id="ordersReturn" enctype="multipart/form-data"
          action="{{ action('OrderReturnController@store', ['id' => $order->id])}}"
          method="POST" class="form-horizontal">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <input type="hidden" value="{{Session::get('uri')}}" id="uri">
        <input type="hidden" name="order_id" value="{{$order->id}}">
        {{ Session::forget('uri') }}
        <div class="row">
            @if($order->items)
                @foreach($order->items as $item)
                    <div class="col-md-6">
                        <h4>
                            <img src="{!! $item->product->getImageUrl() !!}" style="width: 179px; height: 130px;">
                            <strong>{{ $loop->iteration }}. </strong>{{ $item->product->name }}
                            (symbol: {{ $item->product->symbol }})
                        </h4>
                        <div class="row">
                            @foreach($item->realProductPositions() as $position)
                                @if($position->position_quantity == 0)
                                    @continue
                                @endif
                                @if($loop->iteration==1)
                                    <div class="@if($loop->iteration==1) col-md-12 bg-success @else col-md-3 @endif">
                                        @if($loop->iteration==1)
                                            <h4>Towar zostanie zwrócony tutaj</h4>
                                        @endif
                                        Pozycja: {{ $position->lane }} {{ $position->bookstand }} {{ $position->shelf }} {{ $position->position }}
                                        <br/>
                                        Ilość na pozycji: {{ $position->position_quantity }}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="form-group">
                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <label>
                                        <input type="hidden" name="return[{{$loop->iteration}}][check]" value="0">
                                        <input type="hidden" name="return[{{$loop->iteration}}][id]"
                                               @if(count($item->realProductPositions())) @if(isset($order->returnPosition($item->realProductPositions()->first()['id'])->id))value="{{$order->returnPosition($item->realProductPositions()->first()['id'])->id}}" @endif @endif>
                                        <input class="return-check" type="checkbox"
                                               name="return[{{$loop->iteration}}][check]" value="{{$loop->iteration}}"
                                               @if(count($item->realProductPositions()) && $order->returnPosition($item->realProductPositions()[0]['id'])!==null) checked @endif>
                                        Dodaj zwrot
                                        <input type="hidden" name="return[{{$loop->iteration}}][order_id]"
                                               value="{{$order->id}}">
                                        <input type="hidden" name="return[{{$loop->iteration}}][product_id]"
                                               value="{{$item->product_id}}">
                                        @if(count($item->realProductPositions()))
                                            <input type="hidden" name="return[{{$loop->iteration}}][position_id]"
                                                   value="{{$item->realProductPositions()[0]['id']}}">
                                        @endif
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="form-box{{$loop->iteration}}" class="form-return"
                             @if( count($item->realProductPositions()) && $order->returnPosition($item->realProductPositions()[0]['id'])!==null)style="display: block;" @endif>

                            @if(!count($item->realProductPositions()))
                                <div class="row">
                                    <div class="form-group">
                                        <label for="value_of_items_gross" class="col-sm-4 control-label">Pozycja na
                                            magazynie</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" id="lane"
                                                   name="return[{{$loop->iteration}}][positions][lane]">
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" id="bookstand"
                                                   name="return[{{$loop->iteration}}][positions][bookstand]">
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" id="shelf"
                                                   name="return[{{$loop->iteration}}][positions][shelf]">
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" id="position"
                                                   name="return[{{$loop->iteration}}][positions][position]">
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="form-group">
                                    <label for="value_of_items_gross" class="col-sm-4 control-label">Towar nieuszkodzony
                                        (max ilość: {{$item->quantity}})</label>
                                    <div class="col-sm-2">
                                        <input type="number" class="form-control" id="undamaged"
                                               name="return[{{$loop->iteration}}][undamaged]"
                                               @if(
                                                   count($item->realProductPositions()) &&
                                                   $order->returnPosition($item->realProductPositions()[0]['id'])!==null
                                               )
                                                   value="{{$order->returnPosition($item->realProductPositions()[0]['id'])->quantity_undamaged}}"
                                               @endif max="{{$item->quantity}}" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="value_of_items_gross" class="col-sm-4 control-label">Towar uszkodzony
                                        (max ilość: {{$item->quantity}})</label>
                                    <div class="col-sm-2">
                                        <input type="number" class="form-control" id="damaged"
                                               name="return[{{$loop->iteration}}][damaged]"
                                               @if(
                                                   count($item->realProductPositions()) &&
                                                   $order->returnPosition($item->realProductPositions()[0]['id'])!==null
                                               )
                                                   value="{{$order->returnPosition($item->realProductPositions()[0]['id'])->quantity_damaged}}"
                                               @endif max="{{$item->quantity}}" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="value_of_items_gross" class="col-sm-2 control-label">Opis
                                        uszkodzenia</label>
                                    <div class="col-sm-8">
                                <textarea rows="5" cols="40" class="form-control" id="description"
                                          name="return[{{$loop->iteration}}][description]"
                                    @if(
                                        count($item->realProductPositions()) &&
                                        $order->returnPosition($item->realProductPositions()[0]['id'])!==null
                                    )

                                        @endif
                                >@if(
                                    count($item->realProductPositions()) &&
                                    $order->returnPosition($item->realProductPositions()[0]['id'])!==null
                                )
                                        {{$order->returnPosition($item->realProductPositions()[0]['id'])->description}}
                                    @endif</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    Wartość sumy zwrotu
                                    <input class="form-control" placeholder="Wartość sumy zwrotu" name="return[{{$loop->iteration}}][sum_of_return]">

                                    <label for="value_of_items_gross" class="col-sm-2 control-label">Zdjęcie
                                        uszkodzenia</label>

                                    <div class="col-sm-4">
                                        <input type="file"
                                               name="photo[{{$loop->iteration}}]" @if(count($item->realProductPositions()) && $order->returnPosition($item->realProductPositions()[0]['id'])!==null) @endif/>
                                    </div>
                                    <div class="col-sm-6">
                                        @if(count($item->realProductPositions()) && $order->returnPosition($item->realProductPositions()[0]['id'])!==null)
                                            <a rel="popover"
                                               data-img="{{$order->returnPosition($item->realProductPositions()[0]['id'])->getImageUrl()}}"
                                               href="{{ action('OrderReturnController@getImgFullScreen', ['id' => $order->returnPosition($item->realProductPositions()[0]['id'])->id])}}"
                                               target="_blank">
                                                <img
                                                    src="{{$order->returnPosition($item->realProductPositions()[0]['id'])->getImageUrl()}}"
                                                    style="width: 80px;"/>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(($loop->iteration-1)%2)
        </div>
        <div class="row">
            @endif
            @endforeach
            @endif
        </div>
        <button type="submit" form="ordersReturn" id="submit" name="submit" value="update" class="btn btn-primary">
            Zapisz
        </button>
    </form>

@endsection
@section('datatable-scripts')
    <script>
        $('.return-check').change(function () {
            if (this.checked) {
                $('#form-box' + this.value).show();
            } else {
                $('#form-box' + this.value).hide();
            }
        });
        $('a[rel=popover]').popover({
            html: true,
            placement: 'right',
            trigger: 'hover',
            content: function () {
                return '<img src="' + $(this).data('img') + '" />';
            }
        });
    </script>
@endsection
