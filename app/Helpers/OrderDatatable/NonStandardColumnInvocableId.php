<?php

namespace App\Helpers\OrderDatatable;

use App\Helpers\OrderDatatable\NonStandardColumns\AbstractNonStandardColumnInvocable;

class NonStandardColumnInvocableId extends AbstractNonStandardColumnInvocable
{
    public string $view = 'livewire.order-datatable.nonstandard-columns.id';

    protected function getData(array $order): array
    {
        return [];
    }
}
