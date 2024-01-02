<?php

namespace App\Http\Livewire\Traits;

use App\Entities\Order;
use App\Enums\OrderDatatableColumnsEnum;
use App\Services\Label\RemoveLabelService;
use Closure;

trait WithNonstandardColumns
{
    protected function initListeners(): void
    {
        $this->listeners[] = 'removeLabel';
    }

    public function initWithNonstandardColumns(): void
    {
        $this->initListeners();

        foreach (OrderDatatableColumnsEnum::NON_STANDARD_COLUMNS as $columnName => $columnDisplayName) {
            $combinations = $this->generateCombinations($columnName, $columnDisplayName);

            foreach ($combinations as $combination) {
                $columnNameWithValues = $this->replacePlaceholders($columnName, $combination);
                $this->addNonstandardColumn($columnNameWithValues, $this->getColumnCallback($columnDisplayName, $combination));
            }
        }
    }

    protected function generateCombinations(string $columnName, array $columnDisplayName): array
    {
        $matches = [];
        preg_match_all('/{([^}]+)}/', $columnName, $matches);

        if (empty($matches[1])) {
            return [[]];
        }

        $combinations = [[]];
        foreach ($matches[1] as $placeholder) {
            $values = $columnDisplayName['map'][$placeholder] ?? [$placeholder];
            $newCombinations = [];
            foreach ($combinations as $combination) {
                foreach ($values as $value) {
                    $newCombination = $combination;
                    $newCombination[$placeholder] = $value;
                    $newCombinations[] = $newCombination;
                }
            }
            $combinations = $newCombinations;
        }

        return $combinations;
    }

    protected function replacePlaceholders(string $columnName, array $combination): string
    {
        $columnNameWithValues = $columnName;
        foreach ($combination as $placeholder => $value) {
            $columnNameWithValues = str_replace('{' . $placeholder . '}', $value, $columnNameWithValues);
        }

        return $columnNameWithValues;
    }


    protected function getColumnCallback(array $columnDisplayName, array $combination): Closure
    {
        return function (array $order) use ($columnDisplayName, $combination) {
            $data = array_merge($columnDisplayName['data'], $combination);
            $class = new $columnDisplayName['class']();

            return $class($order, $data);
        };
    }

    public function addNonstandardColumn(string $columnName, Closure $callback): void
    {
        foreach($this->orders['data'] as &$order) {
            $order[$columnName] = $callback($order);
        }
    }

    public function removeLabel(int $labelId, int $orderId): void
    {
        $arr = [];
        RemoveLabelService::removeLabels(Order::find($orderId), [$labelId], $arr, [], $this->user->id);
        $this->reloadDatatable();
    }
}
