<?php

namespace App\Helpers\OrderDatatable;

use App\Helpers\OrderDatatable\NonStandardColumns\AbstractNonStandardColumnInvocable;

class NonStandardColumnInvocableBruttoValue extends AbstractNonStandardColumnInvocable
{
    public string $view = 'livewire.order-datatable.nonstandard-columns.brutto-value';
    protected function getData(array $order): array
    {
        return [];
    }
}
