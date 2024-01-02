<?php

namespace App\Http\Livewire\Traits\WithNonstandardColumns;

trait HandlesDynamicColumns
{
    protected function generateDynamicColumns(array $columns): void
    {
        foreach ($columns as $columnName => $columnDisplayName) {
            $this->processColumnName($columnName, $columnDisplayName);
        }
    }

    protected function processColumnName(string $columnName, array $columnDisplayName): void
    {
        $combinations = CombinationGenerator::generate($columnName, $columnDisplayName);
        foreach ($combinations as $combination) {
            $this->addNonstandardColumn(
                $combination['name'],
                $this->getColumnCallback($columnDisplayName, $combination['values'])
            );
        }
    }

    protected function getColumnCallback(array $columnDisplayName, array $values): \Closure
    {
        return function (array $order) use ($columnDisplayName, $values) {
            return ColumnProcessor::process($order, $columnDisplayName, $values);
        };
    }

    public function addNonstandardColumn(string $columnName, Closure $callback): void
    {
        foreach ($this->orders['data'] as &$order) {
            $order[$columnName] = $callback($order);
        }
    }
}
