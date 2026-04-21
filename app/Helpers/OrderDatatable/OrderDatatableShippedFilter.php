<?php

namespace App\Helpers\OrderDatatable;

use App\Helpers\interfaces\AbstractNonStandardColumnFilter;

class OrderDatatableShippedFilter extends AbstractNonStandardColumnFilter
{
    public string $sessionName = 'wyjechalo';

    /**
     * @inheritDoc
     */
    public function applyFilter(mixed $query, string $columnName): mixed
    {
        return $this->getFilterValue() ? $query->whereHas('packages', function ($q) {
            $q->where('delivery_courier_name', $this->getFilterValue())->whereNotNull('delivery_date');
        }) : $query;
    }

    /**
     * @inheritDoc
     */
    public function renderFilter(): string
    {
        return view(
            'livewire.order-datatable.nonstandard-columns.filters.shipped',
            [
                'updateFilterMethodName' => $this->getUpdateFilterMethodName(),
                'updateFilterPropertyName' => $this->updateMethodNameOnComponent,
            ]
        )->render();
    }
}
