@extends("layouts.datatable")

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-window-list"></i> Zwróć płatność allegro
    </h1>
@endsection

@section('table')
    @if($order->items)
        @foreach($order->items as $item)
            <div class="col-md-6">
                <h4>
                    <img src="{!! $item->product->getImageUrl() !!}" style="width: 179px; height: 130px;">
                    <strong>{{ $loop->iteration }}. </strong>{{ $item->product->name }}
                    (symbol: {{ $item->product->symbol }})
                </h4>
            </div>
        @endforeach
    @endif
@endsection
