<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

class NonStandardColumnInvocableProductionDate extends AbstractNonStandardColumnInvocable
{
    protected string $view = 'orderdatatable.nonstandardcolumns.production-date';

    protected function getData(array $order): array
    {
        return [
            'order' => $order,
        ];
    }
}
