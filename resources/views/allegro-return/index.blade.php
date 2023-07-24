@extends("layouts.datatable")

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-window-list"></i> Zwróć płatność allegro
    </h1>
@endsection

@section('table')
    @if($order->items)
        <form enctype="multipart/form-data" action="{{ action('AllegroReturnPaymentController@store', ['orderId' => $order->id])}}" method="POST" class="form-horizontal">
            @csrf
            <div class="grid">
                @if(count($existingAllegroReturns) > 0)
                    <div class="alert alert-warning">
                        <strong>Uwaga!</strong> Istnieją już zwroty dla tego zamówienia.
                    </div>
                @endif
                @foreach($order->items as $item)
                <div style="display: flex;">
                    <div style="width: 60%">
                        <h4 style="display: flex;">
                            <img src="{!! $item->product->getImageUrl() !!}" style="width: 50%; height: 130px;">
                            <div style="width: 50%"><strong>{{ $loop->iteration }}. </strong>{{ $item->product->name }}
                            (symbol: {{ $item->product->symbol }})</div>
                        </h4>
                    </div>
                    <div style="width: 40%">
                        <div style="margin-bottom: 5px;">
                            <input class="return-check" type="checkbox"
                                    name="return[{{$item->product->symbol}}][check]">
                            Dodaj zwrot
                            <input class="return-quantity" type="number" min="1" max="{{ $item->quantity }}"
                                    name="return[{{$item->product->symbol}}][quantity]" value="{{ $item->quantity }}" disabled="true">
                            sztuk
                        </div>
                        <div>
                            <input class="return-deduction-check" type="checkbox"
                                    name="return[{{$item->product->symbol}}][deductionCheck]" disabled="true">
                            Potrącić kwotę?
                            <input class="return-deduction" type="number" min="0" step="0.01"
                                    name="return[{{$item->product->symbol}}][deduction]" disabled="true" value="29.90">
                            Wartość potrącenia
                        </div>
                        <input type="hidden" name="return[{{$item->product->symbol}}][price]" value={{$item->gross_selling_price_commercial_unit}}>
                    </div>
                </div>
                <hr />
                @endforeach
                <button type="submit" class="btn btn-primary pull-right">Zwróć</button>
            </div>
        </form>
    @endif
@endsection

@section('datatable-scripts')
    <script>
        $(document).ready(function() {
            $('.return-check').change(function() {
                var quantityInput = $(this).parent().find('.return-quantity');
                var deductionCheck = $(this).parent().parent().find('.return-deduction-check');
                var deductionInput = $(this).parent().parent().find('.return-deduction');
                if ($(this).is(':checked')) {
                    deductionCheck.prop('disabled', false);
                    quantityInput.prop('disabled', false);
                    if (deductionCheck.is(':checked')) {
                        deductionInput.prop('disabled', false);
                    }
                } else {
                    deductionCheck.prop('disabled', true);
                    quantityInput.prop('disabled', true);
                    deductionInput.prop('disabled', true);
                }
            });
            $('.return-deduction-check').change(function() {
                var quantityInput = $(this).parent().parent().find('.return-quantity');
                var deductionInput = $(this).parent().find('.return-deduction');
                if ($(this).is(':checked')) {
                    deductionInput.prop('disabled', false);
                } else {
                    deductionInput.prop('disabled', true);
                }
            });
        });
    </script>
@endsection
