<?php

namespace App\Helpers\OrderDatatable;

use App\Helpers\interfaces\AbstractNonStandardColumnFilter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class OrderDatatableLabelFilter extends AbstractNonStandardColumnFilter
{
    public string $sessionName = 'labels';

    /**
     * @param mixed $query
     * @param string $columnName
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function applyFilter(mixed $query, string $columnName): mixed
    {
        return $query->whereHas('labels', function ($query) {
            $query->find(session()->get($this->sessionName));
        });
    }

    public function updateFilter(array $data): void
    {
        // TODO: Implement updateFilter() method.
    }

    public function renderFilter(): string
    {
        return view('livewire.order-datatable.nonstandard-columns.filters.labels', ['sessionName' => $this->sessionName])->render();
    }
}
