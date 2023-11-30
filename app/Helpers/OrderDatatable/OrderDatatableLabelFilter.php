<?php

namespace App\Helpers\OrderDatatable;

use App\Helpers\interfaces\AbstractNonStandardColumnFilter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class OrderDatatableLabelFilter extends AbstractNonStandardColumnFilter
{
    public string $sessionName = '';

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->sessionName = $data['labelGroupName'];

        parent::__construct($data);
    }

    /**
     * Apply filter to query
     *
     * @param mixed $query
     * @param string $columnName
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function applyFilter(mixed $query, string $columnName): mixed
    {
        $filterValue = $this->getFilterValue();

        return $filterValue ? $query->whereHas('labels', function ($query) use ($filterValue) {
            $query->where('labels.id', $filterValue);
        }) : $query;
    }

    /**
     * Render filter component
     *
     * @return string
     */
    public function renderFilter(): string
    {
        return view('livewire.order-datatable.nonstandard-columns.filters.labels', ['sessionName' => $this->sessionName, 'data' => $this->data])->render();
    }
}
