<?php

namespace App\Helpers\interfaces;

abstract class AbstractNonStandardColumnFilter
{
    public string $sessionName;

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
     */
    public function updateFilter(array $data): void
    {
        session()->put($this->sessionName, $data);
    }

    /**
     * Render filter blade
     *
     * @return string
     */
    public abstract function renderFilter(): string;
}
