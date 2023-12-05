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
            // Update the filter for the current key
            $column = OrderDatatableColumn::where('label', $key)->first();

            if ($column->resetFilters) {
                OrderDatatableColumn::all()->each(fn ($column) => $column->update(['filter' => '']));
            }

            $column->update(['filter' => $filter]);

            // Check if the filter has nested arrays and update them accordingly
            if (is_array($filter)) {
                $this->updateNestedFilters($key, $filter);
            }
        }
    }

    protected function updateNestedFilters($parentKey, $filters): void
    {
        foreach ($filters as $subKey => $subFilter) {
            // Build the nested key using dot notation
            $nestedKey = $parentKey . '.' . $subKey;

            // Update the filter for the nested key
            OrderDatatableColumn::where('label', $nestedKey)->update(['filter' => $subFilter]);

            // Check if the nested filter has further nesting
            if (is_array($subFilter)) {
                $this->updateNestedFilters($nestedKey, $subFilter);
            }
        }
    }

}
