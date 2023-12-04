<?php

namespace App\Http\Livewire\Traits;

use App\Enums\OrderDatatableColumnsEnum;
use App\Helpers\interfaces\AbstractNonStandardColumnFilter;
use App\Helpers\OrderDatatableNonstandardFiltersHelper;

/**
 * Add extra functionality to Livewire component using its state and methods It depents on OrderDatatableColumnsEnum class
 *
 * @see OrderDatatableColumnsEnum
 *
 * All classes for non standard columns sorting must extends AbstractNonStandardColumnFilter
 *
 * @see AbstractNonStandardColumnFilter
 *
 * @category Trait
 */
trait WithNonStandardColumnsSorting
{
    public array $updateFilters = [];

    /**
     * WithNonStandardColumnsSorting extends Livewire component and adds nonstandard columns sorting functionality to it
     *
     * @return void
     */
    public function initWithNonStandardColumnsSorting(): void
    {
        foreach (OrderDatatableNonstandardFiltersHelper::composeClasses() as $columnName => $nonStandardColumnFilterClass) {
            $this->addNonStandardFilter($columnName, $nonStandardColumnFilterClass);
        }
    }


    /**
     * Add nonstandard column filter to datatable
     *
     * @param string $columnName
     * @param AbstractNonStandardColumnFilter $nonStandardColumnSorter
     * @return void
     */
    public function addNonStandardFilter(string $columnName, AbstractNonStandardColumnFilter $nonStandardColumnSorter): void
    {
        $this->columns = array_map(function ($column) use ($columnName, $nonStandardColumnSorter) {
            $string = str_replace('\\', '-', $nonStandardColumnSorter::class);

            $nonStandardColumnSorter->setUpdateFilterMethodNameOnComponent(
                'callOtherClassMethod("' . $string . '" , "updateFilter", "' . $columnName . '")'
            );

            if ($column['label'] === $columnName) {
                $column['filterComponent'] = $nonStandardColumnSorter->renderFilter();
            }

            $componentPropertyName = $columnName;
            $this->updateFilters[$componentPropertyName] = $nonStandardColumnSorter->getFilterValue();

            return $column;
        }, $this->columns);
    }

    public function callOtherClassMethod(string $class, string $method, $columnName, array $params = []): mixed
    {
        $class = str_replace('-', '\\', $class);
        return app()->make($class, ['sessionName' => $columnName, 'data' => []])->$method($this->updateFilters[$columnName]);
    }
}

