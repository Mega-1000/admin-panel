<?php

namespace App\Services\OrderDatatable;

use App\Entities\Order;
use App\Enums\OrderDatatableColumnsEnum;
use App\Helpers\OrderDatatableNonstandardFiltersHelper;
use App\OrderDatatableColumn;
use App\Repositories\OrderDatatableColumns;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class OrderDatatableRetrievingService
{
    public static array $orders = [];

    /**
     * Fetch orders for datatable for current user save it in $orders property witch is static
     *
     * @return array
     */
    public function fetchOrders(): array
    {
        $q = Order::query();
        $q->with(['labels', 'labels.labelGroup', 'invoiceValues', 'payments', 'items', 'allegroGeneralExpenses']);

        $columns = OrderDatatableColumn::where('filter', '!=', '')->get();
        $columns = $columns->filter(function ($column) {
            return !in_array($column->label, array_keys(OrderDatatableColumnsEnum::NON_STANDARD_FILTERS_CLASSES));
        });

        foreach ($columns as $column) {
            $q->where($column->label, 'like', '%' . $column->filter . '%');
        }

        foreach (OrderDatatableNonstandardFiltersHelper::composeClasses() as $columnName => $nonStandardColumnFilterClass) {
            $q = $nonStandardColumnFilterClass->applyFilter($q, $columnName);
        }

        try {
            self::$orders = $q->paginate(10)->toArray();
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            self::$orders = [];
        }
    }

    /**
     * Get orders for datatable for current user
     *
     * @return array
     */
    public function getOrders(): array
    {
        if (empty(static::$orders)) {
            $this->fetchOrders();
        }


        return self::$orders;
    }

    /**
     * Get columns for datatable for current user
     *
     * @return array[]
     */
    public static function getColumnNames(): array
    {
        $dtColumns = OrderDatatableColumn::where('hidden', false)->get()->toArray();

        if (count($dtColumns) === 0) {
            $dtColumns = OrderDatatableColumnsEnum::DEFAULT_COLUMNS;

            OrderDatatableColumns::reCreateForUser($dtColumns, auth()->id());
        }

        return $dtColumns;
    }
}
