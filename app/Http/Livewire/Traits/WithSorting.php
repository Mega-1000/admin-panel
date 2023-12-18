<?php

namespace App\Http\Livewire\Traits;

use App\OrderDatatableColumn;
use App\Services\OrderDatatable\OrderDatatableRetrievingService;

trait WithSorting
{
    /**
     * Columns for datatable
     *
     * @var array
     */
    public array $columns = [];

    /**
     * Filters for datatable aplied at the moment
     *
     * @var array
     */
    public array $filters = [];

    /**
     * Re render filters uses class state to re render filters
     *
     * @param bool $applyFiltersFromQuery
     * @return void
     */
    public function reRenderFilters(bool $applyFiltersFromQuery = true): void
    {
        $this->listeners[] = 'resetFilters';

        $this->columns = OrderDatatableRetrievingService::getColumnNames();
        $this->filters = array_combine(array_column($this->columns, 'label'), array_column($this->columns, 'filter'));

        if ($applyFiltersFromQuery) {
            $this->applyFiltersFromQuery();
        }
    }

    /**
     * Update column order backend
     *
     * @return void
     */
    public function updatedFilters(): void
    {
        foreach ($this->filters as $key => $filter) {
            $column = OrderDatatableColumn::where('label', $key)->first();

            if ($column && $column->resetFilters && $filter !== $column->filter) {
                OrderDatatableColumn::all()->each(fn ($column) => $column->update(['filter' => '']));
                $column->update(['filter' => $filter]);

                return;
            }

            $column?->update(['filter' => $filter]);
            if (is_array($filter)) {
                $this->updateNestedFilters($key, $filter);
            }

        }
    }

    /**
     * Update nested filters
     *
     * @param string $parentKey
     * @param array $filters
     * @return void
     */
    protected function updateNestedFilters(string $parentKey, array $filters): void
    {
        foreach ($filters as $subKey => $subFilter) {
            // Build the nested key using dot notation
            $nestedKey = $parentKey . '.' . $subKey;

            OrderDatatableColumn::where('label', $nestedKey)->update(['filter' => $subFilter]);

            // Check if the nested filter has further nesting
            if (is_array($subFilter)) {
                $this->updateNestedFilters($nestedKey, $subFilter);
            }
        }
    }


    /**
     * @return void
     */
    public function applyFiltersFromQuery(): void
    {
        $query = request()->query();

        foreach ($this->filters as $key => $filter) {
            $key = str_replace('.', '_', $key);

            if (array_key_exists($key, $query)) {
                OrderDatatableColumn::where('label', str_replace('_', '.', $key))->first()->update(['filter' => $query[$key]]);
                $this->reRenderFilters(false);
            }
        }

        $this->semiReloadDatatable(['reloadFilters' => false]);
    }

    public function resetFilters(): void
    {
        OrderDatatableColumn::all()->each(fn ($column) => $column->update(['filter' => '']));
        $this->semiReloadDatatable();
    }

}
