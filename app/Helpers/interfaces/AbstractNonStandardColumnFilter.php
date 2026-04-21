<?php

namespace App\Helpers\interfaces;

use App\OrderDatatableColumn;

/**
 * Abstract class for non standard column filters
 *
 * @category Helper
 */
abstract class AbstractNonStandardColumnFilter
{
    public string $sessionName = '';
    public string $updateMethodNameOnComponent;
    public function __construct(
        public array $data,
    ) {}


    /**
     * Apply filter
     *
     * @param mixed $query
     * @param string $columnName
     * @return mixed
     */
    public abstract function applyFilter(mixed $query, string $columnName): mixed;

    /**
     * Update filter
     *
     * @param string $data
     * @return void
     */
    public function updateFilter(string $data): void
    {
        auth()->user()->orderDatatableColumns()->where('label', $this->sessionName)->update([
            'filter' => $data,
        ]);
    }

    /**
     * Render filter blade
     *
     * @return string
     */
    public abstract function renderFilter(): string;

    /**
     * Get filter value
     *
     * @return string
     */
    public function getFilterValue(): string
    {
        return auth()->user()->orderDatatableColumns()->where('label', $this->sessionName)->first()?->filter ?? '';
    }

    /**
     * Get update filter method name
     *
     * @return string
     */
    public function getUpdateFilterMethodName(): string
    {
        return 'updateFilters.'. $this->sessionName;
    }

    /**
     * @param string $updateMethodNameOnComponent
     * @return void
     */
    public function setUpdateFilterMethodNameOnComponent(string $updateMethodNameOnComponent): void
    {
        $this->updateMethodNameOnComponent = $updateMethodNameOnComponent;
    }
}
