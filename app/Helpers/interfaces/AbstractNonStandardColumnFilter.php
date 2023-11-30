<?php

namespace App\Helpers\interfaces;

use App\OrderDatatableColumn;
use Illuminate\Contracts\Container\BindingResolutionException;


/**
 * Abstract class for non standard column filters
 *
 * @category Helper
 */
abstract class AbstractNonStandardColumnFilter
{
    public string $sessionName;
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
     * @param array $data
     * @return void
     * @throws BindingResolutionException
     */
    public function updateFilter(array $data): void
    {
        OrderDatatableColumn::where('label', $this->sessionName)->update([
            'filter' => $data['filter'],
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
        return OrderDatatableColumn::where('label', $this->sessionName)->first()?->filter ?? '';
    }
}
