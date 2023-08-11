@extends("layouts.datatable")

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-window-list"></i> Zwróć płatność allegro
    </h1>
@endsection

@section('table')
    @if($order->items)
        <form enctype="multipart/form-data" action="{{ action('AllegroReturnPaymentController@store', ['order' => $order])}}" method="POST" class="form-horizontal">
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
                        <div style="width: 25%">
                            <div style="display: flex; margin-right: 10px">
                                <img src="{!! $item->product->getImageUrl() !!}" style="width: 179px; height: 130px;" />
                                <h4 style="margin-left: 10px"><strong>{{ $loop->iteration }}. </strong>{{ $item->product->name }}
                                (symbol: {{ $item->product->symbol }})</h4>
                            </div>
                        </div>
                        <div style="width: 75%; display: flex; justify-content: space-around">
                            <div style="width: 15%">
                                <p>Ilość zamówiona: {{ $item->quantity }}</p>
                                <p>Ilość zwrócona nieuszkodzona: {{ $item->orderReturn->quantity_undamaged }}</p>
                                <input type="hidden" name="returns[{{$item->product->symbol}}][quantityUndamaged]" value="{{ $item->orderReturn->quantity_undamaged }}">
                                <p>Ilość zwrócona uszkodzona: {{ $item->orderReturn->quantity_damaged }}</p>
                                <input type="hidden" name="returns[{{$item->product->symbol}}][quantityDamaged]" value="{{ $item->orderReturn->quantity_damaged }}">
                                <input type="hidden" name="returns[{{$item->product->symbol}}][price]" value={{$item->gross_selling_price_commercial_unit}}>
                                <input type="hidden" name="returns[{{$item->product->symbol}}][name]" value="{{ $item->product->name }}">
                                <p>Cena: {{ $item->gross_selling_price_commercial_unit }}</p>
                            </div>
                            <div style="width: 30%">
                                @if($item->orderReturn->quantity_undamaged > 0)
                                    <div>
                                        <input type="checkbox" id="undamagedDeductionCheck-{{$item->product->symbol}}"
                                                name="returns[{{$item->product->symbol}}][undamagedDeductionCheck]">
                                        <label for="undamagedDeductionCheck-{{$item->product->symbol}}">Zaznacz aby potrącić kwotę od towaru nieuszkodzonego</label>
                                    </div>
                                    <div>
                                        <input type="number" min="0" step="0.01" max="{{ $item->gross_selling_price_commercial_unit * $item->orderReturn->quantity_undamaged }}"
                                                name="returns[{{$item->product->symbol}}][undamagedDeduction]" disabled value="29.90" id="undamagedDeduction-{{$item->product->symbol}}">
                                        <label for="undamagedDeduction-{{$item->product->symbol}}">Wartość potrącenia od towaru nieuszkodzonego</label>
                                    </div>
                                    <p>
                                        Wartość zwrotu towaru nieuszkodzonego: 
                                        <span id="undamagedValue-{{$item->product->symbol}}">{{ $item->gross_selling_price_commercial_unit * $item->orderReturn->quantity_undamaged }}</span>
                                    </p>
                                @else
                                    <p>Brak towaru nieuszkodzonego do zwrotu</p>
                                @endif
                            </div>
                            <div style="width: 30%">
                                @if($item->orderReturn->quantity_damaged > 0)
                                    <div>
                                        <input type="checkbox" id="returnDamagedCheck-{{$item->product->symbol}}"
                                                name="returns[{{$item->product->symbol}}][returnDamagedCheck]">
                                        <label for="returnDamagedCheck-{{$item->product->symbol}}">Zwróć także za towar uszkodzony</label>
                                    </div>
                                    <div>
                                        <input type="number" min="0" step="0.01" max="{{ $item->gross_selling_price_commercial_unit * $item->orderReturn->quantity_damaged }}"
                                                name="returns[{{$item->product->symbol}}][damagedDeduction]" disabled value="0.00" id="damagedDeduction-{{$item->product->symbol}}">
                                        <label for="damagedDeduction-{{$item->product->symbol}}">Wartość potrącenia od towaru uszkodzonego</label>
                                    </div>
                                    <p>
                                        Wartość zwrotu towaru uszkodzonego: 
                                        <span id="damagedValue-{{$item->product->symbol}}">0.00</span>
                                    </p>
                                @else
                                    <p>Brak towaru uszkodzonego do zwrotu</p>
                                @endif
                            </div>
                            <div style="width: 25%">
                                <p>
                                    Suma potrąceń dla asortymentu:
                                    <span id="deductionSum-{{$item->product->symbol}}">0.00</span>
                                <p>
                                    Wartość zwrotu dla asortymentu: 
                                    <span id="value-{{$item->product->symbol}}">{{ $item->gross_selling_price_commercial_unit * $item->orderReturn->quantity_undamaged }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <hr />
                @endif
                @endforeach
                <button type="submit" class="btn btn-primary pull-right">Zwróć</button>
            </div>
        </form>
    @endif
@endsection

@section('datatable-scripts')
    <script>
        // TODO: fix for those that don't have one of undamaged/damaged 

        $(document).ready(function() {
            @foreach($order->items as $item)
            @if($item->orderReturn != null)
                @if($item->orderReturn->quantity_undamaged > 0)
                    $(`#undamagedDeduction-${$.escapeSelector('{{$item->product->symbol}}')}`).ready(function() {
                        $(this).val(Math.min(29.90, {{$item->gross_selling_price_commercial_unit}} * {{$item->orderReturn->quantity_undamaged}}));
                    })

                    $(`#undamagedDeductionCheck-${$.escapeSelector('{{$item->product->symbol}}')}`).change(function() {
                        const isChecked = $(this).is(':checked');
                        const valueEl = $(`#undamagedValue-${$.escapeSelector('{{$item->product->symbol}}')}`);

                        const deductionInput = $(`#undamagedDeduction-${$.escapeSelector('{{$item->product->symbol}}')}`);
                        deductionInput.prop('disabled', !isChecked);
                        
                        let newValue = {{$item->gross_selling_price_commercial_unit}} * {{$item->orderReturn->quantity_undamaged}};
                        newValue = isChecked ? newValue - parseFloat(deductionInput.val()) : newValue;
                        newValue = newValue.toFixed(2)
                        valueEl.text(newValue);
                        valueEl.trigger('change');

                        const damagedDeductionInput = $(`#damagedDeduction-${$.escapeSelector('{{$item->product->symbol}}')}`);
                        const returnDamagedCheck = $(`#returnDamagedCheck-${$.escapeSelector('{{$item->product->symbol}}')}`);
                        const deductionSumEl = $(`#deductionSum-${$.escapeSelector('{{$item->product->symbol}}')}`);

                        if (!isChecked) {
                            if (!returnDamagedCheck.is(':checked')) {
                                deductionSumEl.text("0.00");
                                return;
                            }

                            const damagedDeduction = parseFloat(damagedDeductionInput.val());
                            deductionSumEl.text(damagedDeduction.toFixed(2));
                            return;
                        }

                        const undamagedDeduction = parseFloat(deductionInput.val());
                        if (!returnDamagedCheck.is(':checked')) {
                            deductionSumEl.text(undamagedDeduction.toFixed(2));
                            return;
                        }

                        const damagedDeduction = parseFloat(damagedDeductionInput.val());
                        deductionSumEl.text((undamagedDeduction + damagedDeduction).toFixed(2));
                    });
                    $(`#undamagedDeduction-${$.escapeSelector('{{$item->product->symbol}}')}`).change(function() {
                        const valueEl = $(`#undamagedValue-${$.escapeSelector('{{$item->product->symbol}}')}`);

                        let value = $(this).val();
                        const max = parseFloat($(this).prop('max'));
                        if (parseFloat($(this).val()) > max) {
                            $(this).val(max);
                            value = max;
                        }
                        const newValue = ({{$item->gross_selling_price_commercial_unit}} * {{$item->orderReturn->quantity_undamaged}} - value).toFixed(2);
                        valueEl.text(newValue);
                        valueEl.trigger('change');
                    });

                    $(`#undamagedValue-${$.escapeSelector('{{$item->product->symbol}}')}`).change(function() {
                        const undamagedValue = parseFloat($(this).text());
                        const damagedValue = parseFloat($(`#damagedValue-${$.escapeSelector('{{$item->product->symbol}}')}`).text());
                        const value = undamagedValue + damagedValue;
                        $(`#value-${$.escapeSelector('{{$item->product->symbol}}')}`).text(value.toFixed(2));
                    });
                @endif
                @if($item->orderReturn->quantity_damaged > 0)
                    $(`#returnDamagedCheck-${$.escapeSelector('{{$item->product->symbol}}')}`).change(function() {
                        const isChecked = $(this).is(':checked');
                        const deductionInput = $(`#damagedDeduction-${$.escapeSelector('{{$item->product->symbol}}')}`);
                        deductionInput.prop('disabled', !isChecked);
                        const valueEl = $(`#damagedValue-${$.escapeSelector('{{$item->product->symbol}}')}`);
                        if (!isChecked) {
                            valueEl.text("0.00");
                            valueEl.trigger('change');
                        } else {
                            const newValue = {{$item->gross_selling_price_commercial_unit}} * {{$item->orderReturn->quantity_damaged}} - parseFloat(deductionInput.val());
                            valueEl.text(newValue.toFixed(2));
                            valueEl.trigger('change');
                        }

                        const undamagedDeductionInput = $(`#undamagedDeduction-${$.escapeSelector('{{$item->product->symbol}}')}`);
                        const undamagedDeductionCheck = $(`#undamagedDeductionCheck-${$.escapeSelector('{{$item->product->symbol}}')}`);
                        const deductionSumEl = $(`#deductionSum-${$.escapeSelector('{{$item->product->symbol}}')}`);

                        if (!isChecked) {
                            if (!undamagedDeductionCheck.is(':checked')) {
                                deductionSumEl.text("0.00");
                                return;
                            }

                            const undamagedDeduction = parseFloat(undamagedDeductionInput.val());
                            deductionSumEl.text(undamagedDeduction.toFixed(2));
                            return;
                        }

                        const damagedDeduction = parseFloat(deductionInput.val());
                        if (!undamagedDeductionCheck.is(':checked')) {
                            deductionSumEl.text(damagedDeduction.toFixed(2));
                            return;
                        }

                        const undamagedDeduction = parseFloat(undamagedDeductionInput.val());
                        deductionSumEl.text((undamagedDeduction + damagedDeduction).toFixed(2));
                    });

                    $(`#damagedDeduction-${$.escapeSelector('{{$item->product->symbol}}')}`).change(function() {
                        const valueEl = $(`#damagedValue-${$.escapeSelector('{{$item->product->symbol}}')}`);

                        let value = $(this).val();
                        const max = parseFloat($(this).prop('max'));
                        if (parseFloat($(this).val()) > max) {
                            $(this).val(max);
                            value = max;
                        }
                        const newValue = ({{$item->gross_selling_price_commercial_unit}} * {{$item->orderReturn->quantity_damaged}} - value).toFixed(2);
                        valueEl.text(newValue);
                        valueEl.trigger('change');

                        const undamagedDeductionInput = $(`#undamagedDeduction-${$.escapeSelector('{{$item->product->symbol}}')}`);
                        const undamagedDeductionCheck = $(`#undamagedDeductionCheck-${$.escapeSelector('{{$item->product->symbol}}')}`);
                        const deductionSumEl = $(`#deductionSum-${$.escapeSelector('{{$item->product->symbol}}')}`);

                        if (!undamagedDeductionCheck.is(':checked')) {
                            deductionSumEl.text(value);
                            return;
                        }

                        const undamagedDeduction = parseFloat(undamagedDeductionInput.val());
                        deductionSumEl.text((undamagedDeduction + value).toFixed(2));
                    });

                    $(`#damagedValue-${$.escapeSelector('{{$item->product->symbol}}')}`).change(function() {
                        const undamagedValue = parseFloat($(`#undamagedValue-${$.escapeSelector('{{$item->product->symbol}}')}`).text());
                        const damagedValue = parseFloat($(this).text());
                        const value = undamagedValue + damagedValue;
                        $(`#value-${$.escapeSelector('{{$item->product->symbol}}')}`).text(value.toFixed(2));
                    });
                @endif
            @endif
            @endforeach
        });
    </script>
@endsection
