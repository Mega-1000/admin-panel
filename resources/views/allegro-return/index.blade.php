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
                        <div style="width: 40%">
                            <div style="display: flex; margin-right: 10px">
                                <img src="{!! $item->product->getImageUrl() !!}" style="width: 179px; height: 130px" />
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
                            <div style="width: 10%">
                                <p>
                                    Suma potrąceń dla asortymentu:
                                    <span id="deductionSum-{{$item->product->symbol}}">0.00</span>
                                <p>
                                    Wartość zwrotu dla asortymentu:
                                    <span id="value-{{$item->product->symbol}}" class="returnValue">{{ $item->gross_selling_price_commercial_unit * $item->orderReturn->quantity_undamaged }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <hr />
                @endif
                @endforeach
                <div style="display: flex; justify-content: space-between">
                    <p>
                        Wartość całego zwrotu:
                        <span id="totalValue">{{ $order->items->sum(function($item) { return $item->gross_selling_price_commercial_unit * $item->orderReturn->quantity_undamaged; }) }}</span>
                    </p>
                    <button type="submit" class="btn btn-primary">Zwróć</button>
                </div>

                @include('fast-response.partials.fast-response-send-box', ['order' => $order])
            </div>
        </form>
    @endif
@endsection

@section('datatable-scripts')
    <script src="/js/helpers/fastResponseSendBox.js"></script>
    <script>
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
                        valueEl.text(newValue.toFixed(2));
                        valueEl.trigger('change');

                        updateDeductionSum('{{$item->product->symbol}}', {{$item->orderReturn->quantity_damaged}}, {{$item->orderReturn->quantity_undamaged}});
                    });
                    $(`#undamagedDeduction-${$.escapeSelector('{{$item->product->symbol}}')}`).change(function() {
                        const valueEl = $(`#undamagedValue-${$.escapeSelector('{{$item->product->symbol}}')}`);

                        let value = parseFloat($(this).val());
                        const max = parseFloat($(this).prop('max'));
                        if (value > max) {
                            $(this).val(max);
                            value = max;
                        }
                        const newValue = ({{$item->gross_selling_price_commercial_unit}} * {{$item->orderReturn->quantity_undamaged}} - value).toFixed(2);
                        valueEl.text(newValue);
                        valueEl.trigger('change');

                        updateDeductionSum('{{$item->product->symbol}}', {{$item->orderReturn->quantity_damaged}}, {{$item->orderReturn->quantity_undamaged}});
                    });

                    $(`#undamagedValue-${$.escapeSelector('{{$item->product->symbol}}')}`).change(function() {
                        changeSingularItemReturnValue('{{$item->product->symbol}}', {{$item->orderReturn->quantity_damaged}}, {{$item->orderReturn->quantity_undamaged}});
                    });
                @endif
                @if($item->orderReturn->quantity_damaged > 0)
                    $(`#returnDamagedCheck-${$.escapeSelector('{{$item->product->symbol}}')}`).change(function() {
                        const isChecked = $(this).is(':checked');
                        const deductionInput = $(`#damagedDeduction-${$.escapeSelector('{{$item->product->symbol}}')}`);
                        deductionInput.prop('disabled', !isChecked);
                        const valueEl = $(`#damagedValue-${$.escapeSelector('{{$item->product->symbol}}')}`);

                        const newValue = isChecked ? {{$item->gross_selling_price_commercial_unit}} * {{$item->orderReturn->quantity_damaged}} - parseFloat(deductionInput.val()) : 0;

                        valueEl.text(newValue.toFixed(2));
                        valueEl.trigger('change');

                        updateDeductionSum('{{$item->product->symbol}}', {{$item->orderReturn->quantity_damaged}}, {{$item->orderReturn->quantity_undamaged}});
                    });

                    $(`#damagedDeduction-${$.escapeSelector('{{$item->product->symbol}}')}`).change(function() {
                        const valueEl = $(`#damagedValue-${$.escapeSelector('{{$item->product->symbol}}')}`);

                        let value = parseFloat($(this).val());
                        const max = parseFloat($(this).prop('max'));
                        if (parseFloat($(this).val()) > max) {
                            $(this).val(max);
                            value = max;
                        }
                        const newValue = ({{$item->gross_selling_price_commercial_unit}} * {{$item->orderReturn->quantity_damaged}} - value).toFixed(2);
                        valueEl.text(newValue);
                        valueEl.trigger('change');

                        const deductionSumEl = $(`#deductionSum-${$.escapeSelector('{{$item->product->symbol}}')}`);

                        updateDeductionSum('{{$item->product->symbol}}', {{$item->orderReturn->quantity_damaged}}, {{$item->orderReturn->quantity_undamaged}});
                    });

                    $(`#damagedValue-${$.escapeSelector('{{$item->product->symbol}}')}`).change(function() {
                        changeSingularItemReturnValue('{{$item->product->symbol}}', {{$item->orderReturn->quantity_damaged}}, {{$item->orderReturn->quantity_undamaged}});
                    });
                @endif
            @endif
            $(`#value-${$.escapeSelector('{{$item->product->symbol}}')}`).change(function() {
                let totalValue = 0;

                $('.returnValue').each(function() {
                    const value = parseFloat($(this).text());
                    totalValue += value;
                });

                $('#totalValue').text(totalValue.toFixed(2));
            });
            @endforeach

            function updateDeductionSum(symbol, quantity_damaged, quantity_undamaged) {
                const deductionSumEl = $(`#deductionSum-${$.escapeSelector(symbol)}`);

                let finalValue = 0;

                if (quantity_undamaged > 0) {
                    const undamagedDeductionInput = $(`#undamagedDeduction-${$.escapeSelector(symbol)}`);
                    const undamagedDeductionCheck = $(`#undamagedDeductionCheck-${$.escapeSelector(symbol)}`);

                    finalValue += undamagedDeductionCheck.is(':checked') ? parseFloat(undamagedDeductionInput.val()) : 0;
                }

                if (quantity_damaged > 0) {
                    const damagedDeductionInput = $(`#damagedDeduction-${$.escapeSelector(symbol)}`);
                    const returnDamagedCheck = $(`#returnDamagedCheck-${$.escapeSelector(symbol)}`);

                    finalValue += returnDamagedCheck.is(':checked') ? parseFloat(damagedDeductionInput.val()) : 0;
                }

                deductionSumEl.text(finalValue.toFixed(2));
            }

            function calculateSingularItemReturnTotalValue(symbol, quantity_damaged, quantity_undamaged) {
                let totalValue = 0;

                if (quantity_undamaged > 0) {
                    const undamagedValue = parseFloat($(`#undamagedValue-${$.escapeSelector(symbol)}`).text());
                    totalValue += undamagedValue;
                }

                if (quantity_damaged > 0) {
                    const damagedValue = parseFloat($(`#damagedValue-${$.escapeSelector(symbol)}`).text());
                    totalValue += damagedValue;
                }

                return totalValue;
            }

            function changeSingularItemReturnValue(symbol, quantity_damaged, quantity_undamaged) {
                const value = calculateSingularItemReturnTotalValue(symbol, quantity_damaged, quantity_undamaged);
                const valueEl = $(`#value-${$.escapeSelector(symbol)}`);
                valueEl.text(value.toFixed(2));
                valueEl.trigger('change');
            }
        });
    </script>
@endsection
