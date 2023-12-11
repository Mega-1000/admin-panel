<?php

namespace App\Enums;

use App\Helpers\interfaces\AbstractNonStandardColumnFilter;
use App\Helpers\OrderDatatable\NonStandardColumnInvocableBruttoValue;
use App\Helpers\OrderDatatable\NonStandardColumnInvocableCheckbox;
use App\Helpers\OrderDatatable\NonStandardColumnInvocableId;
use App\Helpers\OrderDatatable\NonStandardColumns\AbstractNonStandardColumnInvocable;
use App\Helpers\OrderDatatable\NonStandardColumns\NonStandardColumnInvocableActions;
use App\Helpers\OrderDatatable\NonStandardColumns\NonStandardColumnInvocableDepositPaid;
use App\Helpers\OrderDatatable\NonStandardColumns\NonStandardColumnInvocableInvoiceValues;
use App\Helpers\OrderDatatable\NonStandardColumns\NonStandardColumnInvocableLabels;
use App\Helpers\OrderDatatable\NonStandardColumns\NonStandardColumnInvocableNonShipped;
use App\Helpers\OrderDatatable\NonStandardColumns\NonStandardColumnInvocableOfferBalance;
use App\Helpers\OrderDatatable\NonStandardColumns\NonStandardColumnInvocableShipped;
use App\Helpers\OrderDatatable\OrderDatatableLabelFilter;
use App\Helpers\OrderDatatable\OrderDatatableNonShippedFilter;
use App\Helpers\OrderDatatable\OrderDatatableShippedFilter;

enum OrderDatatableColumnsEnum
{
    /**
     * Default columns for datatable
     */
    const DEFAULT_COLUMNS = [
        ['filter' => '', 'order' => 1, 'size' => 100, 'label' => 'checkbox'],
        ['filter' => '', 'order' => 2, 'size' => 100, 'label' => 'id', 'resetFilters' => true],
        ['filter' => '', 'order' => 3, 'size' => 150, 'label' => 'created_at'],
        ['filter' => '', 'order' => 4, 'size' => 100, 'label' => 'akcje'],
        ['filter' => '', 'order' => 5, 'size' => 100, 'label' => 'labels-platnosci'],
        ['filter' => '', 'order' => 6, 'size' => 100, 'label' => 'labels-produkcja'],
        ['filter' => '', 'order' => 7, 'size' => 100, 'label' => 'labels-transport'],
        ['filter' => '', 'order' => 8, 'size' => 100, 'label' => 'labels-info dodatkowe'],
        ['filter' => '', 'order' => 9, 'size' => 100, 'label' => 'labels-fakury zakupu'],
        ['filter' => '', 'order' => 10, 'size' => 100, 'label' => 'wyjechalo'],
        ['filter' => '', 'order' => 11, 'size' => 100, 'label' => 'nie-wyjechalo'],
        ['filter' => '', 'order' => 12, 'size' => 100, 'label' => 'bilans-oferty'],
        ['filter' => '', 'order' => 13, 'size' => 100, 'label' => 'wartosc brutto sprzedazy'],
        ['filter' => '', 'order' => 14, 'size' => 100, 'label' => 'zaliczka-wplacona'],
        ['filter' => '', 'order' => 15, 'size' => 100, 'label' => 'pozostalo-do-zaplaty'],
        ['filter' => '', 'order' => 16, 'size' => 100, 'label' => 'customer.addresses.0.phone', 'resetFilters' => true],
        ['filter' => '', 'order' => 17, 'size' => 100, 'label' => 'allegro_form_id'],
        ['filter' => '', 'order' => 18, 'size' => 100, 'label' => 'allegro_payment_id', 'resetFilters' => true],
        ['filter' => '', 'order' => 19, 'size' => 100, 'label' => 'customer.nick_allegro'],
        ['filter' => '', 'order' => 20, 'size' => 100, 'label' => 'customer.login'],
        ['filter' => '', 'order' => 20, 'size' => 100, 'label' => 'warehouse'],
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
            'class' => OrderDatatableShippedFilter::class,
            'data' => ['labelGroupName' => 'wyjechalo']
        ],
        'nie-wyjechalo' => [
            'class' => OrderDatatableNonShippedFilter::class,
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
            'class' => NonStandardColumnInvocableShipped::class,
            'data' => []
        ],
        'nie-wyjechalo' => [
            'class' => NonStandardColumnInvocableNonShipped::class,
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
        'labels-{name}' => [
            'class' => NonStandardColumnInvocableLabels::class,
            'data' => [
                'labelGroupName' => '{name}'
            ],
            'map' => [
                'name' => [
                    'platnosci',
                    'produkcja',
                    'transport',
                    'info dodatkowe',
                    'fakury zakupu',
                ]
            ],
        ],
        'wartosc brutto sprzedazy' => [
            'class' => NonStandardColumnInvocableBruttoValue::class,
        ],
        'checkbox' => [
            'class' => NonStandardColumnInvocableCheckbox::class,
            'data' => [],
        ],
        'id' => [
            'class' => NonStandardColumnInvocableId::class,
            'data' => [],
        ],
        'warehouse' => [
            'class' => NonStandardColumnInvocableWarehouse::class,
            'data' => [],
        ],
    ];


    /**
     * List of additional views columns mapping
     *
     * @var array<string>
     */
    const ADDITIONAL_VIEWS_COLUMNS = [
        'customer.addresses.0.phone' => 'livewire.order-datatable.nonstandard-columns.additionals.phone-nof-no',
    ];
}
