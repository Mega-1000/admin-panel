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
            <div class="mt-5">
                <hr>
                @foreach($item->product->stock->position as $productStockPosition)
                    <input type="checkbox" id="position[{{ $item->id }}][{{ $productStockPosition->id }}]" name="position[{{ $item->id }}][{{ $productStockPosition->id }}]">{{ $productStockPosition->id }}
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
