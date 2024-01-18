<?php

namespace App\Repositories;

use App\Enums\OrderDatatableColumnsEnum;
use App\OrderDatatableColumn;
use Illuminate\Support\Collection;

class OrderDatatableColumns
{
    /**
     * @param int $userId
     * @return void
     */
    public static function deleteAllRecords(int $userId): void
    {
        auth()->user()->orderDatatableColumns->each(fn($column) => $column->delete());
    }

    public static function reCreateForUser(array $dtColumns, int $userId): void
    {
        self::deleteAllRecords($userId);

        foreach ($dtColumns as $column) {
            OrderDatatableColumn::create($column + ['user_id' => $userId]);
        }
    }

    public static function getAllStandardColumns(): Collection
    {
        $columns = OrderDatatableColumn::where('filter', '!=', '')->get();

        return $columns->filter(function ($column) {
            return !in_array($column->label, array_keys(OrderDatatableColumnsEnum::NON_STANDARD_FILTERS_CLASSES));
        });
    }
}
