<Table>
    <tbody>
    <Tr>
        <Th/>
        <th colSpan='4'>Jednostki</th>
    </Tr>
    <Tr>
        <Th/>
        <Th>Handlowa [{{$item->product->packing->unit_commercial}}]</Th>
        <Th>Obliczeniowa [{{$item->product->packing->calculation_unit}}]</Th>
        <Th>Podstawowa [{{$item->product->packing->unit_basic}}]</Th>
        @if($bw > 0)
            <Th>Zbiorcza [{{$item->product->packing->unit_of_collective}}]</Th>
        @endif
        @if($bx > 0 && $item->net_purchase_price_the_largest_unit > 0)
            <Th>Globalna [{{$item->product->packing->unit_biggest}}]</Th>
        @endif
    </Tr>
    <Tr>
        <Th>Cena jednostkowa netto</Th>
        <Th>
            <input disabled
                   value="{{$item->net_purchase_price_commercial_unit}}"
            />
        </Th>
        <Th>
            <input disabled
                   value="{{$item->net_purchase_price_calculated_unit}}"
            />
        </Th>
        <Th>
            <input disabled
                   value="{{$item->net_purchase_price_basic_unit}}"
            />
        </Th>
        @if($bw > 0)
            <Th>
                <input disabled
                       value="{{$item->net_purchase_price_aggregate_unit}}"
                />
            </Th>
        @endif
        @if($bx > 0 && $item->net_purchase_price_the_largest_unit > 0)
            <Th>
                <input disabled
                       value="{{$item->net_purchase_price_the_largest_unit}}"
                />
            </Th>
        @endif
        <Th/>
    </Tr>
    <Tr>
        <Th>Ceny jednostkowa brutto:</Th>
        <Th>
            <input disabled
                   value="{{$item->net_purchase_price_commercial_unit * 1.23}}"
            />
        </Th>
        <Th>
            <input disabled
                   value="{{$item->net_purchase_price_calculated_unit  * 1.23}}"
            />
        </Th>
        <Th>
            <input disabled
                   value="{{$item->net_purchase_price_basic_unit * 1.23}}"
            />
        </Th>
        @if($bw > 0)

            <Th>
                <input disabled
                       value="{{$item->net_purchase_price_aggregate_unit * 1.23}}"
                />
            </Th>
        @endif
        @if($bx > 0 && $item->net_purchase_price_the_largest_unit > 0)
            <Th>
                <input disabled
                       value="{{$item->net_purchase_price_the_largest_unit * 1.23}}"
                />
            </Th>
        @endif
    </Tr>
    <Tr>
        <Th>Ilość</Th>
        <Th>
            <input disabled
                   value="{{$item->quantity}}"
            />
        </Th>
        <Th>
            <input disabled
                   value={{number_format($item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack / $item->product->packing->unit_consumption, 2)}}
            />
        </Th>
        <Th>
            <input disabled
                   value={{$item->quantity / $item->product->packing->numbers_of_basic_commercial_units_in_pack}}
            />
        </Th>
        @if($bw > 0)
            <Th>
                <input disabled
                       value={{number_format($item->quantity / $item->product->packing->number_of_sale_units_in_the_pack, 2)}}
                />
            </Th>
        @endif
        @if($bx > 0 && $item->net_purchase_price_the_largest_unit > 0)
            <Th>
                <input disabled
                       value={{number_format($item->quantity / $item->product->packing->number_of_trade_items_in_the_largest_unit, 2)}}
                />
            </Th>
        @endif
    </Tr>
    <Tr>
        <Th>Wartość Netto</Th>
        <Th>
            <input disabled
                   value="{{$item->price}}"
            />
        </Th>
        <Th>
            <input disabled
                   value={{$item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack / $item->product->packing->unit_consumption * $item->net_purchase_price_calculated_unit}}
            />
        </Th>
        <Th>
            <input disabled
                   value={{$item->quantity / $item->product->packing->numbers_of_basic_commercial_units_in_pack * $item->net_purchase_price_basic_unit}}
            />
        </Th>
        @if($bw > 0)
            <Th>
                <input disabled
                   value={{number_format($item->quantity / $item->product->packing->number_of_sale_units_in_the_pack * $item->net_purchase_price_aggregate_unit, 2)}}
                />
            </Th>
        @endif
        @if($bx > 0 && $item->net_purchase_price_the_largest_unit > 0)
            <Th>
                <input disabled
                       value={{number_format($item->quantity / $item->product->packing->number_of_trade_items_in_the_largest_unit * $item->net_purchase_price_the_largest_unit, 2)}}
                />
            </Th>
        @endif
    </Tr>
    <Tr>
        <Th>Wartość Brutto</Th>
        <Th>
            <input disabled
                   value="{{$item->price * 1.23}}"
            />
        </Th>
        <Th>
            <input disabled
                   value={{1.23 * $item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack / $item->product->packing->unit_consumption * $item->net_purchase_price_calculated_unit}}
            />
        </Th>
        <Th>
            <input disabled
                   value={{1.23 * $item->quantity / $item->product->packing->numbers_of_basic_commercial_units_in_pack * $item->net_purchase_price_basic_unit}}
            />
        </Th>
        @if($bw > 0)
            <Th>
                <input disabled
                       value={{number_format(1.23 * $item->quantity / $item->product->packing->number_of_sale_units_in_the_pack * $item->net_purchase_price_aggregate_unit, 2)}}
                />
            </Th>
        @endif
        @if($bx > 0 && $item->net_purchase_price_the_largest_unit > 0)
            <Th>
                <input disabled
                       value={{number_format(1.23 * $item->quantity / $item->product->packing->number_of_trade_items_in_the_largest_unit * $item->net_purchase_price_the_largest_unit, 2)}}
                />
            </Th>
        @endif
    </Tr>
    </tbody>
</Table>
