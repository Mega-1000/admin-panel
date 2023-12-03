<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

class NonStandardColumnInvocableLabels extends AbstractNonStandardColumnInvocable
{
    protected string $view = 'livewire.order-datatable.nonstandard-columns.labels';

    protected function getData(array $order): array
    {
        return [
            'labelGroupName' => $this->data['labelGroupName']
        ];
    }
}
