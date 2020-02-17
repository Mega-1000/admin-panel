<body>
<p>
    <strong>Poniżej znajduje się oferta nr: {{$order->id}}</strong>
</p>

<table id="productsTable" class="table table1 table-venice-blue productsTableEdit">
    <tbody id="products-tbody">
    @php
        $gross_purchase_sum = 0;
        $net_purchase_sum = 0;
        $gross_selling_sum = 0;
        $net_selling_sum = 0;
        $weight = 0;
    @endphp
    @foreach($order->items as $item)
        @php
            $gross_purchase_sum += ($item->net_purchase_price_commercial_unit * $item->quantity * 1.23);
            $net_purchase_sum += $item->net_purchase_price_commercial_unit * $item->quantity ;
            $gross_selling_sum += ($item->net_selling_price_commercial_unit * $item->quantity * 1.23);
            $net_selling_sum += $item->net_selling_price_commercial_unit * $item->quantity;
            $weight += $item->product->weight_trade_unit * $item->quantity;
        @endphp
        <tr class="id row-{{$item->id}}" id="id[{{$item->id}}]">
            <td colspan="4"><span style="text-align:left;"><h4><strong>{{ $loop->iteration }}
                            . </strong>{{ $item->product->name }} (symbol: {{ $item->product->symbol }}) </h4></span>
            </td>
        </tr>
        <tr>
            <td>
            <img width="100%" src="{!! str_replace('C:\\z\\', env('APP_URL') . 'storage/products/', $item->product->url) !!}">
            </td>
        </tr>
        <tr class="id row-{{$item->id}}" id="id[{{$item->id}}]">

            <input name="id[{{$item->id}}]"
                   value="{{ $item->id }}" type="hidden"
                   class="form-control" id="id[{{$item->id}}]">
            <input name="product_id[{{$item->id}}]"
                   value="{{ $item->product_id }}" type="hidden"
                   class="form-control" id="product_id[{{$item->id}}]">

            <input
                    value="{{ $item->quantity }}" type="hidden"
                    class="form-control item_quantity" name="item_quantity[{{$item->id}}]" data-item-id="{{$item->id}}">

            <input name="numbers_of_basic_commercial_units_in_pack[{{$item->id}}]"
                   data-item-id="{{$item->id}}"
                   value="{{ $item->product->packing->numbers_of_basic_commercial_units_in_pack }}" type="hidden"
                   class="form-control numbers_of_basic_commercial_units_in_pack"
                   id="numbers_of_basic_commercial_units_in_pack[{{$item->id}}]">
            <input name="number_of_sale_units_in_the_pack[{{$item->id}}]"
                   data-item-id="{{$item->id}}" value="{{ $item->product->packing->number_of_sale_units_in_the_pack }}"
                   type="hidden"
                   class="form-control number_of_sale_units_in_the_pack"
                   id="number_of_sale_units_in_the_pack[{{$item->id}}]">
            <input name="number_of_trade_items_in_the_largest_unit[{{$item->id}}]"
                   data-item-id="{{$item->id}}"
                   value="{{ $item->product->packing->number_of_trade_items_in_the_largest_unit }}" type="hidden"
                   class="form-control number_of_trade_items_in_the_largest_unit"
                   id="number_of_trade_items_in_the_largest_unit[{{$item->id}}]">
            <input name="unit_consumption[{{$item->id}}]"
                   data-item-id="{{$item->id}}" value="{{ $item->product->packing->unit_consumption }}" type="hidden"
                   class="form-control unit_consumption" id="unit_consumption[{{$item->id}}]">
        </tr>
        <tr>
            <td></td>
            <td>Jednostka handlowa ({{ $item->product->packing->unit_commercial }})</td>
            <td>Jednostka podstawowa ({{$item->product->packing->unit_basic}})</td>
            <td>Jednostka obliczeniowa ({{$item->product->packing->calculation_unit}})</td>
            <td>Jednostka zbiorcza ({{$item->product->packing->unit_of_collective}})</td>
        </tr>
        <tr>
            <td colspan="6">
                Zamawiana ilość
            </td>
        </tr>
        <tr class="selling-row row-{{$item->id}}">
            @foreach($productPacking as $packing)
                @if($packing->product_id === $item->product_id)
                    <td>
                        <input name="unit_commercial[{{$item->id}}]"
                               value="{{$item->quantity . ' ' . $packing->unit_commercial }}" type="text"
                               class="form-control" id="unit_commercial" disabled>
                        <input type="hidden" name="unit_commercial_quantity[{{$item->id}}]"
                               value="{{ $item->quantity }}">
                        <input type="hidden" name="unit_commercial_name[{{$item->id}}]"
                               value="{{ $packing->unit_commercial }}">
                    </td>
                    <td>
                        <input name="unit_basic"
                               value="@if($item->product->packing->numbers_of_basic_commercial_units_in_pack != 0){{$item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack  .' '.$packing->unit_basic }} @else {{0}} @endif"
                               type="text"
                               class="form-control" id="unit_basic" disabled>
                        <input type="hidden" name="unit_basic_units[{{$item->id}}]"
                               value="{{ $item->product->packing->numbers_of_basic_commercial_units_in_pack}}">
                        <input type="hidden" name="unit_basic_name[{{$item->id}}]" value="{{ $packing->unit_basic }}">
                    </td>
                    <td>
                        <input name="calculation_unit[{{$item->id}}]"
                               value="@if(is_numeric($item->product->packing->numbers_of_basic_commercial_units_in_pack) && is_numeric($item->product->packing->unit_consumption)){{ number_format($item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack / $item->product->packing->unit_consumption, 2) .' '.$packing->calculation_unit }} @else {{0}} @endif"
                               type="text"
                               class="form-control" id="calculation_unit" disabled>
                        <input type="hidden" name="calculation_unit_units[{{$item->id}}]"
                               value="{{ $item->product->packing->numbers_of_basic_commercial_units_in_pack}}">
                        <input type="hidden" name="calculation_unit_consumption[{{$item->id}}]"
                               value="{{ $item->product->packing->unit_consumption }}">
                        <input type="hidden" name="calculation_unit_name[{{$item->id}}]"
                               value="{{ $packing->calculation_unit }}">
                    </td>
                    <td>
                        @php
                            if (empty($item->product->packing->number_of_sale_units_in_the_pack))
                                $a = 0;
                            else
                                $a = $item->quantity / $item->product->packing->number_of_sale_units_in_the_pack;
                        @endphp
                        <input name="unit_of_collective[{{$item->id}}]"
                               value="{{ number_format($a, 4) .' '.$packing->unit_of_collective}} " type="text"
                               class="form-control" id="unit_of_collective" disabled>
                        <input type="hidden" name="unit_of_collective_units[{{$item->id}}]"
                               value="{{ $item->product->packing->number_of_sale_units_in_the_pack }}">
                        <input type="hidden" name="unit_of_collective_name[{{$item->id}}]"
                               value="{{ $packing->unit_of_collective }}">
                    </td>
                @endif
            @endforeach
        </tr>
        <tr class="row-{{$item->id}}">
            <th colspan="4" style="text-align: left;">Wartość asortymentu</th>
        </tr>
        <tr class="selling-row row-{{$item->id}}">
            <td>
                <input type="text" class="form-control item-value priceChange" data-item-id="{{$item->id}}" disabled
                       name="item-value"
                       value="{{ number_format(($item->net_selling_price_commercial_unit * $item->quantity * 1.23), 2) }} zł">
            </td>
            <td colspan="3"></td>
        </tr>
        @if(!empty($productsVariation[$item->product->id]))
            <tr>
                <td colspan="4"><h3>Wariacje produktów:</h3></td>
            </tr>
            <tr>
                <td style="width: 15%;">

                    Nazwa
                </td>
                <td style="width: 5%;">

                    Cena sprzedaży brutto jednostki handlowej
                </td>
                <td style="width: 5%;">

                    Cena sprzedaży brutto jednostki podstawowej
                </td>
                <td style="width: 5%;">

                    Cena sprzedaży brutto jednostki obliczeniowej
                </td>
                <td style="width: 5%;">

                    Wartość danego asortymentu
                </td>
                <td style="width: 5%;">

                    Różnica
                </td>
                <td style="width: 5%;">

                    Odległość
                </td>
                <td>

                    Recenzja
                </td>
                <td>

                    Jakość
                </td>
                <td>

                    Jakość do ceny
                </td>
                <td>

                    Przybliżona wartość towarów danego magazynu do darmowej przesyłki
                </td>
            </tr>
            <hr>
            @foreach($productsVariation[$item->product->id] as $variation)
                <tr class="row-{{$variation['id']}}">
                    <td>

                        {{$variation['name']}}
                    </td>
                    <td>

                        {{$variation['gross_selling_price_commercial_unit']}}
                    </td>
                    <td>

                        {{$variation['gross_selling_price_basic_unit']}}
                    </td>
                    <td>

                        {{$variation['gross_selling_price_calculated_unit']}}
                    </td>
                    <td>

                        {{$variation['sum']}}
                    </td>
                    <td>

                        @if(strstr($variation['different'], '-') != false)
                            <span style="color:red;">{{(float)$variation['different']}}</span>
                        @else
                            <span style="color:green;">+{{(float)$variation['different']}}</span>
                        @endif
                    </td>
                    <td>{{(int)$variation['radius']}} km</td>
                    <td>{{$variation['review']}}</td>
                    <td>{{$variation['quality']}}</td>
                    <td>{{$variation['quality_to_price']}}</td>
                    <td>{{$variation['value_of_the_order_for_free_transport']}}</td>
                </tr>
                <tr>
                    Uwagi: {{$variation['comments']}}
                </tr>
                <hr>
            @endforeach
        @endif
    @endforeach
    </tbody>
</table>
<div class="form-group">
    <input type="hidden" class="form-control" id="weight" name="weight"
           value="{{ $order->weight ?? '' }}">
</div>
<div class="form-group">
    <input type="hidden" class="form-control priceChange" id="profit" disabled name="profit"
           value="{{ number_format($gross_selling_sum - $gross_purchase_sum, 2) }}">
</div>
<div class="form-group">
    <input type="hidden" class="form-control priceChange" id="total_price" disabled name="total_price"
           value="{{ $order->total_price ?? '' }}">
</div>
@if(!empty($allProductsFromSupplier))
    <h3>Suma wszystkich towarów dla danych producentów</h3>
    <table class="table table1 table-venice-blue productsTableEdit">
        <thead>
        <tr>
            <th>Symbol dostawcy</th>
            <th>Wartość sumaryczna</th>
            <th>Różnica wartości do wskazanego producenta w zamówieniu</th>
            <th>Odległość od magazynu</th>
            <th>Telefon do konsultanta</th>
            <th>

                Recenzja
            </th>
            <th>

                Jakość
            </th>
            <th>

                Jakość do ceny
            </th>
            <th>

                Przybliżona wartość towarów danego magazynu do darmowej przesyłki
            </th>
        </tr>
        <hr>
        </thead>
        <tbody>
        @foreach($allProductsFromSupplier as $productsGroup)
            @foreach($productsGroup as $groupName => $productSupplier)
                <tr style="text-transform: uppercase;">{{$groupName}}</tr>
                <tr>
                    <td>{{$productSupplier['product_name_supplier']}}</td>
                    <td>{{$productSupplier['sum']}}</td>
                    <td>
                        @if(strstr($productSupplier['different'], '-') != false)
                            <span style="color:red;">{{(float)$productSupplier['different']}}</span>
                        @else
                            <span style="color:green;">+{{(float)$productSupplier['different']}}</span>
                        @endif
                    </td>
                    <td>{{(int)$productSupplier['radius']}} km</td>
                    <td>{{$productSupplier['phone']}}</td>
                    <td>{{$productSupplier['review']}}</td>
                    <td>{{$productSupplier['quality']}}</td>
                    <td>{{$productSupplier['quality_to_price']}}</td>
                    <td>{{$productSupplier['value_of_the_order_for_free_transport']}}</td>
                </tr>
                <tr>
                    {{$productSupplier['comments']}}
                </tr>
                <hr>
            @endforeach
        @endforeach
        </tbody>
    </table>
@endif
<p>
    Z pozdrowieniami
    ZESPOL MEGA1000
</p>
</body>
