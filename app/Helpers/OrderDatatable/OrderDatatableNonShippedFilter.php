<?php

namespace App\Helpers\OrderDatatable;

use App\Helpers\interfaces\AbstractNonStandardColumnFilter;

class OrderDatatableNonShippedFilter extends AbstractNonStandardColumnFilter
{
    public string $sessionName = 'nie-wyjechalo';

    /**
     * @inheritDoc
     */
    public function applyFilter(mixed $query, string $columnName): mixed
    {
        return $this->getFilterValue() ? $query->whereHas('packages', function ($q) {
            $q->where('delivery_courier_name', $this->getFilterValue())->whereNull('delivery_date');
        }) : $query;
    }

    /**
     * @inheritDoc
     */
    public function renderFilter(): string
    {
        return view(
            'livewire.order-datatable.nonstandard-columns.filters.non-shipped',
            [
                'updateFilterMethodName' => $this->getUpdateFilterMethodName(),
                'updateFilterPropertyName' => $this->updateMethodNameOnComponent,
            ]
        )->render();
    }
}
