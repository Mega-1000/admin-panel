<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

class NonStandardColumnInvocableWarehouse extends AbstractNonStandardColumnInvocable
{
    public string $view = 'livewire.order-datatable.nonstandard-columns.warehouse';

    protected function getData(array $order): array
    {
        return [
            'data' => $order
        ];
    }
}
