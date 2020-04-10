<Table>
    <tbody>
    <Tr>
        <Th/>
        <th colSpan='{{4 + ($bw > 0) + ($bx > 0 && $item->net_purchase_price_the_largest_unit_after_discounts > 0)}}'>
            Jednostki
        </th>
    </Tr>
    <Tr>
        <Th/>
        <Th>Handlowa [{{$item->product->packing->unit_commercial}}]</Th>
        <Th>Podstawowa [{{$item->product->packing->unit_basic}}]</Th>
        <Th>Obliczeniowa [{{$item->product->packing->calculation_unit}}]</Th>
        @if($bw > 0)
            <Th>Zbiorcza [{{$item->product->packing->unit_of_collective}}]</Th>
        @endif
        @if($bx > 0 && $item->net_purchase_price_the_largest_unit_after_discounts > 0)
            <Th>Globalna [{{$item->product->packing->unit_biggest}}]</Th>
        @endif
    </Tr>
    <Tr>
        <Th>Cena jednostkowa netto</Th>
        <Th>
            <input disabled
                   value="{{round($item->net_purchase_price_commercial_unit_after_discounts, 2)}}"
            />
        </Th>
        <Th>
            <input disabled
                   value="{{round($item->net_purchase_price_basic_unit_after_discounts, 2)}}"
            />
        </Th>
        <Th>
            <input disabled
                   value="{{round($item->net_purchase_price_calculated_unit_after_discounts, 2)}}"
            />
        </Th>
        @if($bw > 0)
            <Th>
                <input disabled
                       value="{{round($item->net_purchase_price_aggregate_unit_after_discounts, 2)}}"
                />
            </Th>
        @endif
        @if($bx > 0 && $item->net_purchase_price_the_largest_unit_after_discounts > 0)
            <Th>
                <input disabled
                       value="{{round($item->net_purchase_price_the_largest_unit_after_discounts, 2)}}"
                />
            </Th>
        @endif
        <Th/>
    </Tr>
    <Tr>
        <Th>Ceny jednostkowa brutto:</Th>
        <Th>
            <input disabled
                   value="{{number_format($item->net_purchase_price_commercial_unit_after_discounts * 1.23, 2)}}"
            />
        </Th>
        <Th>
            <input disabled
                   value="{{number_format($item->net_purchase_price_basic_unit_after_discounts * 1.23, 2)}}"
            />
        </Th>
        <Th>
            <input disabled
                   value="{{number_format($item->net_purchase_price_calculated_unit_after_discounts  * 1.23, 2)}}"
            />
        </Th>
        @if($bw > 0)

            <Th>
                <input disabled
                       value="{{number_format($item->net_purchase_price_aggregate_unit_after_discounts * 1.23, 2)}}"
                />
            </Th>
        @endif
        @if($bx > 0 && $item->net_purchase_price_the_largest_unit_after_discounts > 0)
            <Th>
                <input disabled
                       value="{{number_format($item->net_purchase_price_the_largest_unit_after_discounts * 1.23, 2)}}"
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
                   value={{number_format($item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack, 3)}}
            />
        </Th>
        <Th>
            <input disabled
                   value={{number_format($item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack / $item->product->packing->unit_consumption, 3)}}
            />
        </Th>
        @if($bw > 0)
            <Th>
                <input disabled
                       value={{number_format($item->quantity * $item->product->packing->number_of_sale_units_in_the_pack, 3)}}
                />
            </Th>
        @endif
        @if($bx > 0 && $item->net_purchase_price_the_largest_unit_after_discounts > 0)
            <Th>
                <input disabled
                       value={{number_format($item->quantity * $item->product->packing->number_of_trade_items_in_the_largest_unit, 3)}}
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
                   value={{$item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack * $item->net_purchase_price_basic_unit_after_discounts}}
            />
        </Th>
        <Th>
            <input disabled
                   value={{$item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack / $item->product->packing->unit_consumption * $item->net_purchase_price_calculated_unit_after_discounts}}
            />
        </Th>
        @if($bw > 0)
            <Th>
                <input disabled
                       value={{number_format($item->quantity * $item->product->packing->number_of_sale_units_in_the_pack * $item->net_purchase_price_aggregate_unit_after_discounts, 2)}}
                />
            </Th>
        @endif
        @if($bx > 0 && $item->net_purchase_price_the_largest_unit_after_discounts > 0)
            <Th>
                <input disabled
                       value={{number_format($item->quantity * $item->product->packing->number_of_trade_items_in_the_largest_unit * $item->net_purchase_price_the_largest_unit_after_discounts, 2)}}
                />
            </Th>
        @endif
    </Tr>
    <Tr>
        <Th>Wartość Brutto</Th>
        <Th>
            <input disabled
                   value="{{number_format($item->price * 1.23, 2)}}"
            />
        </Th>
        <Th>
            <input disabled
                   value={{number_format($item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack * round(1.23 * $item->net_purchase_price_basic_unit_after_discounts, 2), 2)}}
            />
        </Th>
        <Th>
            <input disabled
                   value={{number_format($item->quantity * $item->product->packing->numbers_of_basic_commercial_units_in_pack / $item->product->packing->unit_consumption * round(1.23 * $item->net_purchase_price_calculated_unit_after_discounts, 2), 2)}}
            />
        </Th>
        @if($bw > 0)
            <Th>
                <input disabled
                       value={{number_format($item->quantity * $item->product->packing->number_of_sale_units_in_the_pack * round(1.23 * $item->net_purchase_price_aggregate_unit_after_discounts, 2), 2)}}
                />
            </Th>
        @endif
        @if($bx > 0 && $item->net_purchase_price_the_largest_unit_after_discounts > 0)
            <Th>
                <input disabled
                       value={{number_format($item->quantity * $item->product->packing->number_of_trade_items_in_the_largest_unit * round(1.23 * $item->net_purchase_price_the_largest_unit_after_discounts, 2), 2)}}
                />
            </Th>
        @endif
    </Tr>
    </tbody>
</Table>
