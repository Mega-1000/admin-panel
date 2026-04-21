<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

class NonStandardColumnInvocableActions extends AbstractNonStandardColumnInvocable
{
    protected string $view = 'livewire.order-datatable.nonstandard-columns.actions';

    protected function getData(array $order): array
    {
        return [];
    }
}
