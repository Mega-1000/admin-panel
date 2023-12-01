<?php

namespace App\Helpers\OrderDatatable;

use App\Helpers\interfaces\AbstractNonStandardColumnFilter;

class OrderDatatableShipmentFilter extends AbstractNonStandardColumnFilter
{

    /**
     * @inheritDoc
     */
    public function applyFilter(mixed $query, string $columnName): mixed
    {
        return $query;
    }

    /**
     * @inheritDoc
     */
    public function renderFilter(): string
    {
        return 'okej';
    }
}
