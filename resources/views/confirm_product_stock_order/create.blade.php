@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_payments.create')
    </h1>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
@endsection

@section('table')
    <form action="" method="POST">
        @csrf
        @foreach($order->items as $item)
            <h3>
                {{ $item->product->name }} |
                {{ $item->product->product_name_manufacturer }} |
                {{ $item->product->supplier_product_symbol }} |
                {{ $item->product->packing->unit_commercial }} |
                {{ $item->product->packing->numbers_of_basic_commercial_units_in_pack }} |
                {{ $item->product->packing->unit_of_collective }} |
                {{ $item->product->packing->number_of_sale_units_in_the_pack }}
            </h3>


            <div class="mt-5">
                <hr>
                <table>
                    <thead>
                    <tr>
                        <th>Checkbox</th>
                        <th>Aleja</th>
                        <th>Regał</th>
                        <th>Półka</th>
                        <th>Pozycja</th>
                        <th>Ilość produktów</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($item->product->stock->position as $productStockPosition)
                        <tr>
                            <td>
                                <input type="checkbox" id="position[{{ $item->id }}][{{ $productStockPosition->id }}]" name="position[{{ $item->id }}][{{ $productStockPosition->id }}]">
                            </td>
                            <td>{{ $productStockPosition->lane }}</td>
                            <td>{{ $productStockPosition->bookstand }}</td>
                            <td>{{ $productStockPosition->shelf }}</td>
                            <td>{{ $productStockPosition->position }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <label for="quantity[{{ $item->id }}]">Ilość produktów, przyjętych na stan (domyślnie ilość na zamówieniu)</label>
                <input class="form-control" type="text" name="quantity[{{ $item->id }}]" value="{{ $item->quantity }}">
            </div>
        @endforeach

        <button class="mt-5 btn btn-primary">
            Zapisz
        </button>
    </form>
@endsection

@section('javascript')
    <script>
        function handleCheckbox() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');

            checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                    if (checkbox.checked) {
                        const itemId = checkbox.id.split('[')[1].split(']')[0];
                        const checkboxesInGroup = document.querySelectorAll(`input[id^="position[${itemId}]"]`);
                        checkboxesInGroup.forEach(otherCheckbox => {
                            if (otherCheckbox !== checkbox) {
                                otherCheckbox.checked = false;
                            }
                        });
                    }
                });
            });
        }

        handleCheckbox();
    </script>
@endsection
