<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

class NonStandardColumnInvocableShipped extends AbstractNonStandardColumnInvocable
{
    public function __invoke(array $order): string
    {
        return view('livewire.order-datatable.nonstandard-columns.shipped', compact('order'))->render();

    }
}
