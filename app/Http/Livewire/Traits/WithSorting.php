<?php

namespace App\Http\Livewire\Traits;

use App\OrderDatatableColumn;
use App\Services\OrderDatatable\OrderDatatableRetrievingService;

trait WithSorting
{
    public array $columns = [];
    public array $filters = [];

    /**
     * Re render filters uses class state to re render filters
     */
    public function reRenderFilters(): void
    {
        $this->columns = OrderDatatableRetrievingService::getColumnNames();
        $this->filters = array_combine(array_column($this->columns, 'label'), array_column($this->columns, 'filter'));
    }

    /**
     * Update column order backend
     */
    public function updatedFilters(): void
    {
        foreach ($this->filters as $key => $filter) {
            OrderDatatableColumn::where('label', $key)->update(['filter' => $filter]);
        }
    }
}
