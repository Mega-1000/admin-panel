<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

class NonStandardColumnInvocableNonShipped
{
    public function __invoke(array $order): string
    {
        return view('livewire.order-datatable.nonstandard-columns.non-shipped', compact('order'))->render();
    }
}
