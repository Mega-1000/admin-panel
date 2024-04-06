<?php

namespace App\Http\Livewire\Traits;

use App\OrderDatatableColumn;
use App\Services\OrderDatatable\OrderDatatableRetrievingService;
use Illuminate\Support\Facades\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait WithFilters
{
    public array $columns = [];
    public array $filters = [];
    public bool $anyFiltersApplied = false;

    public function mountWithFilters(): void
    {
        $this->listeners[] = 'resetFilters';
    }

    public function reRenderFilters(): mixed
    {
        $this->initializeColumnsAndFilters();

        if (Request::query('applyFiltersFromQuery') !== 'true') {
            $this->applyFiltersFromQuery();
        }

        return null;
    }

    protected function initializeColumnsAndFilters(): void
    {
        $this->columns = OrderDatatableRetrievingService::getColumnNames($this->user->id);
        $this->filters = array_combine(
            array_column($this->columns, 'label'),
            array_column($this->columns, 'filter')
        );
    }

    public function updatedFilters(): void
    {
        $this->skipRender();
    }

    public function updateFilters(bool $applyFromQuery = true): void
    {
        foreach ($this->filters as $key => $filter) {
            $this->updateColumnFilter($key, $filter, $applyFromQuery);
        }
    }

    protected function updateColumnFilter(string $key, $filter, bool $applyFromQuery): void
    {
        $column = auth()->user()->orderDatatableColumns()->where('label', $key)->first();

        if ($column && $column->resetFilters && $filter !== $column->filter) {
            auth()->user()->orderDatatableColumns()->update(['filter' => '']);
            $column->update(['filter' => $filter]);
            return;
        }

        $column?->update(['filter' => $filter]);

        if (is_array($filter)) {
            $this->resetFilters();

            if (array_key_exists('addresses', $filter)) {
                $filter['addresses'][0]['phone'] = str_replace(' ', '', $filter['addresses'][0]['phone']);
            }

            $this->updateNestedFilters($key, $filter);
        }
    }

    protected function updateNestedFilters(string $parentKey, array $filters): void
    {
        foreach ($filters as $subKey => $subFilter) {
            $nestedKey = $parentKey . '.' . $subKey;
            OrderDatatableColumn::where('label', $nestedKey)->update(['filter' => $subFilter]);

            if (is_array($subFilter)) {
                $this->updateNestedFilters($nestedKey, $subFilter);
            }
        }

        $this->reloadDatatable();
    }

    public function applyFiltersFromQuery(): void
    {
        foreach (Request::query() as $key => $value) {
            $key = str_replace('_', '.', $key);

            if (isset($this->filters[$key])) {
                OrderDatatableColumn::where('label', $key)->where('user_id', auth()->id())->first()->update(['filter' => $value]);
                $this->anyFiltersApplied = true;
            }
        }
    }

    public function resetFilters(): void
    {
        $willAnyFilterBeReset = false;
        foreach (OrderDatatableColumn::all() as $column) {
            if ($column->filter !== '') {
                $willAnyFilterBeReset = true;
                break;
            }
        }

        if (!$willAnyFilterBeReset) {
            $this->skipRender();
            return;
        }

        OrderDatatableColumn::query()->update(['filter' => '']);
    }
}
