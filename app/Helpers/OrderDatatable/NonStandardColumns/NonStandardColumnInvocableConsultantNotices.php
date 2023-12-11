<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

class NonStandardColumnInvocableConsultantNotices extends AbstractNonStandardColumnInvocable
{
    public string $view = 'livewire.order-datatable.nonstandard-columns.consultant-notices';

    protected function getData(array $order): array
    {
        return [
            'order' => $order,
        ];
    }
}
