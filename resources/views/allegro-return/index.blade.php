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
                <hr />
                @foreach($order->items as $item)
                @if($item->orderReturn != null)
                    <div style="display: flex; margin-top: 5px;">
                        <div style="width: 40%">
                            <h4 style="display: flex; margin-right: 10px">
                                <img src="{!! $item->orderReturn->product->getImageUrl() !!}" style="width: 50%; height: 130px;">
                                <div style="width: 50%"><strong>{{ $loop->iteration }}. </strong>{{ $item->product->name }}
                                (symbol: {{ $item->orderReturn->product->symbol }})</div>
                            </h4>
                        </div>
                        <div style="width: 60%; display: flex; justify-content: space-around">
                            <div>
                                <p>Ilość nieuszkodzona: {{ $item->orderReturn->quantity_undamaged }}</p>
                                <input type="hidden" name="return[{{$item->orderReturn->product->symbol}}][quantityUndamaged]" value="{{ $item->orderReturn->quantity_undamaged }}">
                                <p>Ilość uszkodzona: {{ $item->orderReturn->quantity_damaged }}</p>
                                <input type="hidden" name="return[{{$item->orderReturn->product->symbol}}][quantityDamaged]" value="{{ $item->orderReturn->quantity_damaged }}">
                                <input type="hidden" name="return[{{$item->orderReturn->product->symbol}}][price]" value={{$item->gross_selling_price_commercial_unit}}>
                                <input type="hidden" name="return[{{$item->orderReturn->product->symbol}}][name]" value="{{ $item->product->name }}">
                                <p>Cena: {{ $item->gross_selling_price_commercial_unit }}</p>
                            </div>
                            <div>
                                <div>
                                    <input type="checkbox" id="deductionCheck-{{$item->orderReturn->product->symbol}}"
                                            name="return[{{$item->orderReturn->product->symbol}}][deductionCheck]">
                                    <label for="deductionCheck-{{$item->orderReturn->product->symbol}}">Potrącić kwotę?</label>
                                </div>
                                <div>
                                    <input class="return-deduction" type="number" min="0" step="0.01" max="{{ $item->gross_selling_price_commercial_unit * $item->orderReturn->quantity_undamaged }}"
                                            name="return[{{$item->orderReturn->product->symbol}}][deduction]" disabled="true" value="29.90" id="deduction-{{$item->orderReturn->product->symbol}}">
                                    <label for="deduction-{{$item->orderReturn->product->symbol}}">Wartość potrącenia</label>
                                </div>
                                <p>Wartość zwrotu: 
                                    <span id="value-{{$item->orderReturn->product->symbol}}">{{ $item->gross_selling_price_commercial_unit * $item->orderReturn->quantity_undamaged }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                @endforeach
                <button type="submit" class="btn btn-primary pull-right">Zwróć</button>
            </div>
        </form>
    @endif
@endsection

@section('datatable-scripts')
    <script>
        $(document).ready(function() {
            @foreach($order->items as $item)
            @if($item->orderReturn != null)
                $('#deduction-{{$item->orderReturn->product->symbol}}').ready(function() {
                    $(this).val(Math.min(29.90, {{$item->gross_selling_price_commercial_unit}} * {{$item->orderReturn->quantity_undamaged}}));
                })

                $('#deductionCheck-{{$item->orderReturn->product->symbol}}').change(function() {
                    const isChecked = $(this).is(':checked');
                    const valueEl = $('#value-{{$item->orderReturn->product->symbol}}');

                    const deductionInput = $('#deduction-{{$item->orderReturn->product->symbol}}');
                    deductionInput.prop('disabled', !$(this).is(':checked'));
                    
                    let newValue = {{$item->gross_selling_price_commercial_unit}} * {{$item->orderReturn->quantity_undamaged}};
                    newValue = isChecked ? newValue - parseFloat(deductionInput.val()) : newValue;
                    newValue = newValue.toFixed(2)
                    valueEl.text(newValue);
                });
                $('#deduction-{{$item->orderReturn->product->symbol}}').change(function() {
                    const value = $(this).val();
                    const max = parseFloat($(this).prop('max'));
                    if (parseFloat($(this).val()) > max) {
                        $(this).val(max);
                        value = max;
                    }
                    newValue = ({{$item->gross_selling_price_commercial_unit}} * {{$item->orderReturn->quantity_undamaged}} - value).toFixed(2);
                    $('#value-{{$item->orderReturn->product->symbol}}').text(newValue);
                });
            @endif
            @endforeach
        });
    </script>
@endsection
