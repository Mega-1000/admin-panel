<?php

namespace App\Enums;

use App\Helpers\interfaces\AbstractNonStandardColumnFilter;
use App\Helpers\OrderDatatable\OrderDatatableLabelFilter;

enum OrderDatatableColumnsEnum
{
    /**
     * Default columns for datatable
     */
    const DEFAULT_COLUMNS = [
        ['filter' => '', 'order' => 1, 'size' => 100, 'label' => 'id'],
        ['filter' => '', 'order' => 2, 'size' => 150, 'label' => 'created_at'],
        ['filter' => '', 'order' => 3, 'size' => 100, 'label' => 'akcje'],
        ['filter' => '', 'order' => 3, 'size' => 100, 'label' => 'labels-platnosci'],
        ['filter' => '', 'order' => 3, 'size' => 100, 'label' => 'labels-produkcja'],
        ['filter' => '', 'order' => 3, 'size' => 100, 'label' => 'labels-transport'],
        ['filter' => '', 'order' => 3, 'size' => 100, 'label' => 'labels-info dodatkowe'],
        ['filter' => '', 'order' => 3, 'size' => 100, 'label' => 'labels-fakury zakupu'],
    ];

    /**
     * List of classes that extends AbstractNonStandardColumnFilter
     *
     * @var array<AbstractNonStandardColumnFilter>
     */
    const NON_STANDARD_FILTERS_CLASSES = [
        'labels-platnosci' => [
            'class' => OrderDatatableLabelFilter::class,
            'data' => ['labelGroupName' => 'labels-platnosci']
        ],
        'labels-produkcja' => [
            'class' => OrderDatatableLabelFilter::class,
            'data' => ['labelGroupName' => 'labels-produkcja']
        ],
        'labels-transport' => [
            'class' => OrderDatatableLabelFilter::class,
            'data' => ['labelGroupName' => 'labels-transport']
        ],
        'labels-info dodatkowe' => [
            'class' => OrderDatatableLabelFilter::class,
            'data' => ['labelGroupName' => 'labels-info dodatkowe']
        ],
        'labels-fakury zakupu' => [
            'class' => OrderDatatableLabelFilter::class,
            'data' => ['labelGroupName' => 'labels-fakury zakupu']
        ],
    ];
}
