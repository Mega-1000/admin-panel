<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

class NonStandardColumnInvocableInvoiceValues extends AbstractNonStandardColumnInvocable
{
   protected string $view = 'livewire.order-datatable.nonstandard-columns.invoice-values';

    protected function getData(array $order): array
    {
         return [];
    }
}
