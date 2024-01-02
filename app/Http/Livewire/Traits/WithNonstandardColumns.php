<?php

namespace App\Http\Livewire\Traits;

use App\Entities\Order;
use App\Enums\OrderDatatableColumnsEnum;
use App\Services\Label\RemoveLabelService;
use Closure;

trait WithNonstandardColumns
{
    /**
     * WithNonstandardColumns extends Livewire component and adds nonstandard columns functionality to it
     *
     * @return void
     */
    public function initWithNonstandardColumns(): void
    {
        $this->listeners[] = 'removeLabel';
        $this->generateNonstandardColumns();
    }

    /**
     * Generate nonstandard columns based on configurations
     *
     * @return void
     */
    private function generateNonstandardColumns(): void
    {
        foreach (OrderDatatableColumnsEnum::NON_STANDARD_COLUMNS as $columnName => $columnDisplayName) {
            preg_match_all('/{([^}]+)}/', $columnName, $matches);
            $placeholders = $matches[1] ?? [];

            if ($placeholders) {
                $this->processPlaceholders($columnName, $placeholders, $columnDisplayName);
            } else {
                $this->addNonstandardColumn($columnName, $this->createColumnCallback($columnDisplayName));
            }
        }
    }

    /**
     * Process column name placeholders
     *
     * @param string $columnName
     * @param array $placeholders
     * @param array $columnDisplayName
     * @return void
     */
    private function processPlaceholders(string $columnName, array $placeholders, array $columnDisplayName): void
    {
        $combinations = $this->generatePlaceholderCombinations($placeholders, $columnDisplayName);

        foreach ($combinations as $combination) {
            $modifiedColumnName = $this->replacePlaceholdersInColumnName($columnName, $combination);
            $callback = $this->createColumnCallback($columnDisplayName, $combination);
            $this->addNonstandardColumn($modifiedColumnName, $callback);
        }
    }

    /**
     * Generate combinations of placeholders
     *
     * @param array $placeholders
     * @param array $columnDisplayName
     * @return array
     */
    private function generatePlaceholderCombinations(array $placeholders, array $columnDisplayName): array
    {
        $combinations = [[]];
        foreach ($placeholders as $placeholder) {
            $values = $columnDisplayName['map'][$placeholder] ?? [$placeholder];
            $newCombinations = [];

            foreach ($combinations as $combination) {
                foreach ($values as $value) {
                    $newCombinations[] = array_merge($combination, [$placeholder => $value]);
                }
            }

            $combinations = $newCombinations;
        }

        return $combinations;
    }

    /**
     * Replace placeholders in the column name
     *
     * @param string $columnName
     * @param array $combination
     * @return string
     */
    private function replacePlaceholdersInColumnName(string $columnName, array $combination): string
    {
        foreach ($combination as $placeholder => $value) {
            $columnName = str_replace("{{$placeholder}}", $value, $columnName);
        }

        return $columnName;
    }

    /**
     * Create a callback function for a column
     *
     * @param array $columnDisplayName
     * @param array $combination
     * @return Closure
     */
    private function createColumnCallback(array $columnDisplayName, array $combination = []): Closure
    {
        return function (array $order) use ($columnDisplayName, $combination) {
            $data = array_merge($columnDisplayName['data'], $combination);
            $class = new $columnDisplayName['class'](['labelGroupName' => $data['name'] ?? null]);

            return $class($order, $data);
        };
    }

    /**
     * Add nonstandard column to datatable
     *
     * @param string $columnName
     * @param Closure $callback
     * @return void
     */
    public function addNonstandardColumn(string $columnName, Closure $callback): void
    {
        foreach ($this->orders['data'] as &$order) {
            $order[$columnName] = $callback($order);
        }
    }

    /**
     * Remove label from an order
     *
     * @param $labelId
     * @param $orderId
     * @return void
     */
    public function removeLabel($labelId, $orderId): void
    {
        RemoveLabelService::removeLabels(Order::find($orderId), [$labelId], [], [], null);
        $this->reloadDatatable();
    }
}
