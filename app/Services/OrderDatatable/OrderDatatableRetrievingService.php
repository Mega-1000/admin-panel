<?php

namespace App\Services\OrderDatatable;

use App\Entities\Order;
use App\Enums\OrderDatatableColumnsEnum;
use App\OrderDatatableColumn;
use App\Repositories\OrderDatatableColumns;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class OrderDatatableRetrievingService
{
    /**
     * Get orders for datatable for current user
     *
     * @return array
     */
    public function getOrders(): array
    {
        $q = Order::query();

        foreach (OrderDatatableColumn::where('filter', '!=', '')->get() as $column) {
            $q->where($column->label, 'like', '%' . $column->filter . '%');
        }

        try {
            return $q->paginate(session()->get('pageLength', 10))->toArray();
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return $q->paginate(10)->toArray();
        }
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
