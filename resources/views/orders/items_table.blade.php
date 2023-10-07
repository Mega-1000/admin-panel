@foreach($items as $item)
    <table style="width: 100%;">
        @include('orders.single_item', ['item' => $item->product, 'quantity' => $item->quantity, 'orderItem' => $item])
        <tr>
            <td colspan="4">
                <hr/>
            </td>
        </tr>
    </table>

@endforeach
