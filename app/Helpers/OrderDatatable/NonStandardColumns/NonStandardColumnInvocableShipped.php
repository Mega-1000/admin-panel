<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

class NonStandardColumnInvocableShipped extends AbstractNonStandardColumnInvocable
{
    protected string $view = 'livewire.order-datatable.nonstandard-columns.shipped';

    protected function getData(array $order): array
    {
        return [];
    }
}
