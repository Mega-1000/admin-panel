<?php

namespace App\Services\OrderDatatable;

use App\Entities\Order;
use App\OrderDatatableColumns as OrderDatatableColumnsModel;
use Illuminate\Pagination\LengthAwarePaginator;


class OrderDatatableRetrievingService
{
    public function getOrders(): array
    {
        return Order::paginate(10)->toArray();
    }

    /**
     * Get columns for datatable for current user
     *
     * @return array[]
     */
    public static function getColumnNames(): array
    {
        $dtColumns = OrderDatatableColumnsModel::where('hidden', false)->get()->toArray();

        if (count($dtColumns) === 0) {
            $dtColumns = [
                ['name' => 'ID', 'order' => 1, 'size' => 100, 'label' => 'id'],
                ['name' => 'created_at', 'order' => 2, 'size' => 150, 'label' => 'created_at'],
            ];
        }

        return $dtColumns;
    }
}
