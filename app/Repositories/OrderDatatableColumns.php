<?php

namespace App\Repositories;

use App\OrderDatatableColumn;

class OrderDatatableColumns
{
    /**
     * @param int $userId
     * @return void
     */
    public static function deleteAllRecords(int $userId): void
    {
        OrderDatatableColumn::all()->each(fn($column) => $column->delete());
    }

    public static function reCreateForUser(array $dtColumns, int $userId): void
    {
        self::deleteAllRecords($userId);

        foreach ($dtColumns as $column) {
            OrderDatatableColumn::create($column + ['user_id' => $userId]);
        }
    }
}
