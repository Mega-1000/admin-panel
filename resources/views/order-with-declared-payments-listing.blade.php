@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>

<script defer>
    function showHide() {
        setTimeout(() => {
            let elements = document.getElementsByClassName('hidden');

            elements = Array.from(elements);

            for (let i = 0; i < elements.length; i++) {
                elements[i].classList.remove('hidden');
                console.log(elements[i] )
            }
        }, 1000)
    }

    showHide();
</script>
@section('table')
    <table class="table table-bordered">
        <tr>
            <th>
                ID
            </th>
            <th>
                Nazwa
            </th>
            <th>
                Stworzono
            </th>
            <th>
                akcje
            </th>
        </tr>
        @foreach($orders as $order)
            <tr>
                <td>
                    {{ $order->id }}
                </td>
                <td>
                    {{ $order->name }}
                </td>
                <td>
                    {{ $order->created_at }}
                </td>
                <td>
                    <a href="{{ route('orders.edit', ['order_id' => $order->id]) }}" class="btn btn-primary">Zobacz</a>
                </td>
            </tr>
        @endforeach
    </table>

    <div style="display: block !important;" class="d-flex justify-content-center">
        {{ $orders->links() }}
    </div>
@endsection
