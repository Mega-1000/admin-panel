<?php

namespace App\Helpers\OrderDatatable;

use App\Helpers\OrderDatatable\NonStandardColumns\AbstractNonStandardColumnInvocable;

/**
 * Class NonStandardColumnInvocableCheckbox
 * @package App\Helpers\OrderDatatable
 */
class NonStandardColumnInvocableCheckbox extends AbstractNonStandardColumnInvocable
{
    public string $view = 'livewire.order-datatable.nonstandard-columns.checkbox';

    /**
     * @inheritDoc
     */
    protected function getData(array $order): array
    {
        return [];
    }
}
