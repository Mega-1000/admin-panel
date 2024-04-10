<?php

namespace App\Helpers\OrderDatatable;

use App\Helpers\OrderDatatable\NonStandardColumns\AbstractNonStandardColumnInvocable;

class NonStandardColumnInvocableCreatedAt extends AbstractNonStandardColumnInvocable
{
    public string $view = 'livewire.order-datatable.nonstandard-columns.created_at';

    protected function getData(array $order): array
    {
        return [];
    }
}
