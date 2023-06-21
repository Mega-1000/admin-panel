@extends('layouts.datatable')
@section('app-header')
    <style>
        .tags {
            width: 100%;
        }
        .tag {
            width: 50%;
            float: right;
        }
    </style>
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css">

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
@endsection
@section('table')
    <!--- display allOrdersForUser in table -->

    <form id="separate_form" action="{{ route('orderPayments.rebookStoreSingle', ['payment' => $orderPayment->id]) }}" method="post">
        @csrf
        <input class="form-control" id="value_of_rebook" name="value_of_rebook" type="text" placeholder="wartość przeksięgowania">
        <input class="form-control" id="rebook_order_id" name="rebook_order_id" type="text" placeholder="id oferty">

        <input class="btn btn-primary" value="Zapisz" type="submit">
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Data stworzenia oferty</th>
                <th>Status</th>
                <th>Wartość oferty</th>
                <th>Akcje</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($allOrdersForUser as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->created_at }}</td>
                        <td>{{ $order->status->name }}</td>
                        <td>{{ $order->getValue() }}</td>
                        <td>
                            <form action="{{ route('orderPayments.rebookStore', ['order' => $order->id, 'payment' => $orderPayment->id]) }}" method="post" class="d-flex align-items-center">
                                @csrf
                                <input type="text" name="value" value="{{ $orderPayment->amount }}" placeholder="wartość przeksięgowania">
                                <input type="submit" class="btn btn-primary" value="Przeksięguj na to zamówienie">
                            </form>
                        </td>
                    </tr>
        @endforeach
    </table>
@endsection

<script>
    const onSubmit = (e) => {
        e.preventDefault();
        const value = document.getElementById('value_of_rebook').value;
        const orderId = document.getElementById('rebook_order_id').value;

        alert(value);
        const form = document.getElementById('separate_form');
        form.action = `/orderPayments/rebook/${orderId}/${value}`;
    };
</script>
