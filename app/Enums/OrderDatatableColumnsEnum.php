<?php

namespace App\Enums;

enum OrderDatatableColumnsEnum
{
    const DEFAULT_COLUMNS = [
        ['filter' => '', 'order' => 1, 'size' => 100, 'label' => 'id'],
        ['filter' => '', 'order' => 2, 'size' => 150, 'label' => 'created_at'],
        ['filter' => '', 'order' => 3, 'size' => 100, 'label' => 'akcje'],
    ];
}
