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
                <label for="reason">Powód zwrotu</label>
                <select name="reason" id="reason">
                    <option value="REFUND">Zwrot</option>
                    <option value="COMPLAINT">Reklamacja</option>
                    <option value="PRODUCT_NOT_AVAILABLE">Produkt niedostępny</option>
                    <option value="PAID_VALUE_TOO_LOW">Zapłacona kwota za niska</option>
                </select>
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
                            <input class="return-quantity" type="number" min="1" max="{{ $item->quantity }}" id="return-quantity-{{$item->product->symbol}}"
                                    name="return[{{$item->product->symbol}}][quantity]" value="{{ $item->quantity }}" disabled="true">
                            sztuk
                        </div>
                        <div>
                            <input class="return-deduction-check" type="checkbox"
                                    name="return[{{$item->product->symbol}}][deductionCheck]" disabled="true">
                            Potrącić kwotę?
                            <input class="return-deduction" type="number" min="0" step="0.01" max="{{ $item->gross_selling_price_commercial_unit * $item->quantity }}"
                                    name="return[{{$item->product->symbol}}][deduction]" disabled="true" value="29.90">
                            Wartość potrącenia
                        </div>
                        <input type="hidden" name="return[{{$item->product->symbol}}][price]" class="return-price"
                                value={{$item->gross_selling_price_commercial_unit}} id="return-price-{{$item->product->symbol}}">
                        <input type="hidden" class="return-symbol" value="{{$item->product->symbol}}">
                        <input type="hidden" class="return-max-quantity" value="{{$item->quantity}}">
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
            $('.return-deduction').each(function() {
                symbol = $(this).parent().parent().find('.return-symbol').val();
                $(this).val(getDefaultDeductionValue(symbol));
            });

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
            $('.return-quantity').change(function() {
                var deductionInput = $(this).parent().parent().find('.return-deduction');
                var returnPrice = parseFloat($(this).parent().parent().find('.return-price').val());
                var quantity = parseInt($(this).val());
                if (parseFloat(deductionInput.val()) > returnPrice * quantity) {
                    deductionInput.val(returnPrice * quantity);
                }
                deductionInput.prop('max', returnPrice * quantity);
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
            $('.return-deduction').change(function() {
                var returnPrice = parseFloat($(this).parent().parent().find('.return-price').val());
                var quantity = parseInt($(this).parent().parent().find('.return-quantity').val());
                if (parseFloat($(this).val()) > returnPrice * quantity) {
                    $(this).val(returnPrice * quantity);
                }
            });
        });

        function getDefaultDeductionValue(symbol) {
            var price = parseFloat(document.getElementById('return-price-' + symbol).value);
            var quantity = parseInt(document.getElementById('return-quantity-' + symbol).value);
            return Math.min(29.90, quantity * price);
        }
    </script>
@endsection
