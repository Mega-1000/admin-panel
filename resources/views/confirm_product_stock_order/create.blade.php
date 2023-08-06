@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_payments.create')
    </h1>
@endsection

@section('table')
    <form action="" method="POST">
        @csrf
        @foreach($order->items as $item)
            <h3>
                {{ $item->product->name }} |
                {{ $item->product->product_name_manufacturer }} |
                {{ $item->product->supplier_product_symbol }} |
                {{ $item->product->unit_commercial }} |
                {{ $item->product->unit_of_collective }} |
                {{ $item->product->numbers_of_basic_commercial_units_in_pack }} |
                {{ $item->product->number_of_sale_units_in_the_pack }}
            </h3>

            <div class="mt-5">
                <hr>
                @foreach($item->product->stock->position as $productStockPosition)
                    <input type="checkbox" id="position[{{ $item->id }}][{{ $productStockPosition->id }}]" name="position[{{ $item->id }}][{{ $productStockPosition->id }}]">{{ $productStockPosition->lane }} | {{ $productStockPosition->bookstand }} {{ $productStockPosition->shelf }} {{ $productStockPosition->position }}
                     <br>
                @endforeach

                Ilość produktów
                <input class="form-control" type="text" name="quantity[{{ $item->id }}]quantity" value="{{ $item->quantity }}">
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
