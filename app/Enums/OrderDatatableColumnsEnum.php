<?php

namespace App\Enums;

use App\Helpers\OrderDatatable\OrderDatatableLabelFilter;

enum OrderDatatableColumnsEnum
{
    const DEFAULT_COLUMNS = [
        ['filter' => '', 'order' => 1, 'size' => 100, 'label' => 'id'],
        ['filter' => '', 'order' => 2, 'size' => 150, 'label' => 'created_at'],
        ['filter' => '', 'order' => 3, 'size' => 100, 'label' => 'akcje'],
        ['filter' => '', 'order' => 3, 'size' => 100, 'label' => 'labels'],
    ];

    const NON_STANDARD_FILTERS_CLASSES = [
        'labels' => OrderDatatableLabelFilter::class
    ];
}
