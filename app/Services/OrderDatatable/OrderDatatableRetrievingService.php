<?php

namespace App\Services\OrderDatatable;

use App\Entities\Order;
use App\Enums\OrderDatatableColumnsEnum;
use App\Helpers\OrderDatatableNonstandardFiltersHelper;
use App\OrderDatatableColumn;
use App\Repositories\OrderDatatableColumns;
use Illuminate\Database\Eloquent\Builder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class OrderDatatableRetrievingService
{
    public static array $orders = [];

    /**
     * Fetch orders for datatable for current user save it in $orders property witch is static
     *
     * @return void
     */
    public function fetchOrders(): void
    {
        $q = Order::query();
        $q->with([
            'labels',
            'labels.labelGroup',
            'invoiceValues',
            'payments',
            'items',
            'allegroGeneralExpenses',
            'packages',
            'otherPackages',
            'customer',
            'customer.addresses',
        ]);

        $columns = OrderDatatableColumn::where('filter', '!=', '')->get();
        $columns = $columns->filter(function ($column) {
            return !in_array($column->label, array_keys(OrderDatatableColumnsEnum::NON_STANDARD_FILTERS_CLASSES));
        });

        foreach ($columns as $column) {
            if (!$this->isNestedFilter($column)) {
                $q->where($column->label, 'like', '%' . $column->filter . '%');
                continue;
            }

            $query = $this->applyNestedFilter($q, $column);
        }

        foreach (OrderDatatableNonstandardFiltersHelper::composeClasses() as $columnName => $nonStandardColumnFilterClass) {
            $q = $nonStandardColumnFilterClass->applyFilter($q, $columnName);
        }

        try {
            self::$orders = $q->orderBy('created_at', 'desc')->paginate(session()->get('pageLength', 10))->toArray();
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            self::$orders = $q->paginate(10)->toArray();
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

    /**
     * @param mixed $column
     * @return bool
     */
    private function isNestedFilter(mixed $column): bool
    {
        return str_contains($column->label, '.');
    }

    /**
     * @param Builder $q
     * @param mixed $column
     * @return Builder
     */
    private function applyNestedFilter(Builder $q, mixed $column): Builder
    {
        $labelParts = explode('.', $column->label);

        if (is_numeric($labelParts[2])) {
            $q->whereHas($labelParts[0], function ($q) use ($labelParts, $column) {
                $q->whereHas($labelParts[1], function ($q) use ($labelParts, $column) {
                    $q->where($labelParts[3], 'like', '%' . $column->filter . '%');
                });
            });
            return $q;
        }

        return $q;
    }
}
