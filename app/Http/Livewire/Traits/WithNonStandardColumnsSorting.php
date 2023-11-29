<?php

namespace App\Http\Livewire\Traits;

use App\Enums\OrderDatatableColumnsEnum;
use App\Helpers\interfaces\AbstractNonStandardColumnFilter;

trait WithNonStandardColumnsSorting
{

    /**
     * WithNonStandardColumnsSorting extends Livewire component and adds nonstandard columns sorting functionality to it
     *
     * @return void
     */
    public function initWithNonStandardColumnsSorting(): void
    {
        foreach (OrderDatatableColumnsEnum::NON_STANDARD_FILTERS_CLASSES as $columnName => $nonStandardColumnFilterClass) {
            $this->addNonStandardFilter($columnName, new $nonStandardColumnFilterClass());
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
            if ($column['label'] === $columnName) {
                $column['filterComponent'] = $nonStandardColumnSorter->renderFilter();
            }

            return $column;
        }, $this->columns);
    }
}

