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
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Data stworzenia oferty</th>
                <th>Status</th>
                <th>Wartość oferty</th>
                <th>Wartość</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($allOrdersForUser as $order)
                <form action="{{ route('orderPayments.rebookStore', ['order' => $order->id, 'payment' => $orderPayment->id]) }}" method="post">
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->created_at }}</td>
                        <td>{{ $order->status->name }}</td>
                        <td>{{ $order->total_price }}</td>
                        <td><input type="text" name="value" value="{{ $orderPayment->amount }}"></td>
                        <td>
                                @csrf

                                <button role="submit" class="btn btn-primary">Przeksięguj na to zamówienie</button>
                        </td>
                    </tr>
                </form>
            @endforeach
    </table>
@endsection
