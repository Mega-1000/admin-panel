<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

class NonStandardColumnInvocableInvoicesColumn extends AbstractNonStandardColumnInvocable
{
    protected string $view = 'livewire.order-datatable.nonstandard-columns.invoices';

    protected function getData(array $order): array
    {
        return ['order' => $order];
    }
}
