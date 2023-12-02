<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

class NonStandardColumnInvocableDepositPaid extends AbstractNonStandardColumnInvocable
{

    public function __invoke(array $order): string
    {
        return view('livewire.order-datatable.nonstandard-columns.deposit-paid', compact('order'))->render();
    }
}
