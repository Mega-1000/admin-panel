@foreach($items as $item)
    <table border="0" cellpadding="1" cellspacing="1" style="width: 100%;">
        @include('orders.single_item', ['item' => $item->product, 'quantity' => $item->quantity])
        <tr>
            <td colspan="3">
                <hr/>
            </td>
        </tr>
    </table>

@endforeach
