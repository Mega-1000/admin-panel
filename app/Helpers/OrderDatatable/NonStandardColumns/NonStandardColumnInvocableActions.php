<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

class NonStandardColumnInvocableActions extends AbstractNonStandardColumnInvocable
{
    /**
     * @param array $order
     * @return string
     */
    public function __invoke(array $order): string
    {
        return view('livewire.order-datatable.nonstandard-columns.actions', compact('order'))->render();
    }
}
