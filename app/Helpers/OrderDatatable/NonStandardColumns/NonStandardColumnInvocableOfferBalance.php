<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

class NonStandardColumnInvocableOfferBalance extends AbstractNonStandardColumnInvocable
{
    public function __invoke(array $order): string
    {
        return view('livewire.order-datatable.nonstandard-columns.offer-balance', compact('order'))->render();
    }
}
