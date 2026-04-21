<?php

namespace App\Helpers;

use App\Enums\OrderDatatableColumnsEnum;
use App\Helpers\interfaces\AbstractNonStandardColumnFilter;
use Illuminate\Database\Eloquent\Builder;

class OrderDatatableNonstandardFiltersHelper
{
    /**
     * Create array of classes that extends AbstractNonStandardColumnFilter
     *
     * @return array<AbstractNonStandardColumnFilter>
     */
    public static function composeClasses(): array
    {
        $classes = [];

        foreach (OrderDatatableColumnsEnum::NON_STANDARD_FILTERS_CLASSES as $columnName => $classData) {
            $classes[$columnName] = new $classData['class'](
                data: $classData['data']
            );
        }

        return $classes;
    }

    /**
     * Apply non standard filters to query
     *
     * @param Builder $q
     * @return Builder
     */
    public static function applyNonstandardFilters(Builder $q): Builder
    {
        foreach (OrderDatatableNonstandardFiltersHelper::composeClasses() as $columnName => $nonStandardColumnFilterClass) {
            $q = $nonStandardColumnFilterClass->applyFilter($q, $columnName);
        }

        return $q;
    }
}
