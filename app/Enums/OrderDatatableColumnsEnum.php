<?php

namespace App\Enums;

use App\Helpers\interfaces\AbstractNonStandardColumnFilter;
use App\Helpers\OrderDatatable\NonStandardColumns\AbstractNonStandardColumnInvocable;
use App\Helpers\OrderDatatable\NonStandardColumns\NonStandardColumnInvocableActions;
use App\Helpers\OrderDatatable\NonStandardColumns\NonStandardColumnInvocableDepositPaid;
use App\Helpers\OrderDatatable\NonStandardColumns\NonStandardColumnInvocableInvoiceValues;
use App\Helpers\OrderDatatable\NonStandardColumns\NonStandardColumnInvocableOfferBalance;
use App\Helpers\OrderDatatable\OrderDatatableLabelFilter;
use App\Helpers\OrderDatatable\OrderDatatableShipmentFilter;

enum OrderDatatableColumnsEnum
{
    /**
     * Default columns for datatable
     */
    const DEFAULT_COLUMNS = [
        ['filter' => '', 'order' => 1, 'size' => 100, 'label' => 'id'],
        ['filter' => '', 'order' => 2, 'size' => 150, 'label' => 'created_at'],
        ['filter' => '', 'order' => 4, 'size' => 100, 'label' => 'akcje'],
        ['filter' => '', 'order' => 5, 'size' => 100, 'label' => 'labels-platnosci'],
        ['filter' => '', 'order' => 6, 'size' => 100, 'label' => 'labels-produkcja'],
        ['filter' => '', 'order' => 7, 'size' => 100, 'label' => 'labels-transport'],
        ['filter' => '', 'order' => 8, 'size' => 100, 'label' => 'labels-info dodatkowe'],
        ['filter' => '', 'order' => 9, 'size' => 100, 'label' => 'labels-fakury zakupu'],
        ['filter' => '', 'order' => 10, 'size' => 100, 'label' => 'wyjechalo'],
        ['filter' => '', 'order' => 11, 'size' => 100, 'label' => 'nie-wyjechalo'],
        ['filter' => '', 'order' => 12, 'size' => 100, 'label' => 'bilans-oferty'],
        ['filter' => '', 'order' => 14, 'size' => 100, 'label' => 'zaliczka-wplacona'],
        ['filter' => '', 'order' => 14, 'size' => 100, 'label' => 'pozostalo-do-zaplaty'],
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
        'wyjechalo' => [
            'class' => OrderDatatableShipmentFilter::class,
            'data' => ['labelGroupName' => 'wyjechalo']
        ],
        'nie-wyjechalo' => [
            'class' => OrderDatatableShipmentFilter::class,
            'data' => ['labelGroupName' => 'nie-wyjechalo']
        ],
    ];

    /**
     * List of non standard columns classes
     *
     * @var array<AbstractNonStandardColumnInvocable>
     */
    const NON_STANDARD_COLUMNS = [
        'akcje' => [
            'class' => NonStandardColumnInvocableActions::class,
            'data' => []
        ],
        'wyjechalo' => [
            'class' => NonStandardColumnInvocableInvoiceValues::class,
            'data' => []
        ],
        'nie-wyjechalo' => [
            'class' => NonStandardColumnInvocableActions::class,
            'data' => []
        ],
        'pozostalo-do-zaplaty' => [
            'class' => NonStandardColumnInvocableInvoiceValues::class,
            'data' => []
        ],
        'bilans-oferty' => [
            'class' => NonStandardColumnInvocableOfferBalance::class,
            'data' => []
        ],
        'zaliczka-wplacona' => [
            'class' => NonStandardColumnInvocableDepositPaid::class,
            'data' => []
        ],
    ];
}
