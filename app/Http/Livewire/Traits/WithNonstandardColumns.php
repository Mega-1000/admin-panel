<?php

namespace App\Http\Livewire\Traits;

use App\Entities\Order;
use App\Enums\OrderDatatableColumnsEnum;
use App\Services\Label\RemoveLabelService;
use Closure;

trait WithNonstandardColumns
{
    protected function setListeners(): void
    {
        $this->listeners[] = 'removeLabel';
    }

    /**
     * WithNonstandardColumns extends Livewire component and adds nonstandard columns functionality to it
     *
     * @return void
     */
    public function initWithNonstandardColumns(): void
    {
        $this->setListeners();

        foreach (OrderDatatableColumnsEnum::NON_STANDARD_COLUMNS as $columnName => $columnDisplayName) {
            $matches = [];

            // Use a regular expression to find all placeholders in curly braces
            preg_match_all('/{([^}]+)}/', $columnName, $matches);

            if (!empty($matches[1])) {
                // Iterate over each placeholder and generate combinations
                $combinations = $this->generateCombinations($matches[1]);

                // Generate column names with replaced values and add functionality
                foreach ($combinations as $combination) {
                    $columnNameWithValues = $columnName;
                    foreach ($combination as $placeholder => $value) {
                        $columnNameWithValues = str_replace("$placeholder", $value, $columnNameWithValues);
                    }

                    $columnNameWithValues = str_replace(['{', '}'], '', $columnNameWithValues);

                    $this->settleNonStandardColumns($columnNameWithValues, $columnDisplayName, $combination);
                }
            } else {
                $this->addNonstandardColumn($columnName, function (array $order) use ($columnDisplayName) {
                    $class = new $columnDisplayName['class']();
                    return $class($order);
                });
            }
        }
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
        foreach($this->orders['data'] as &$order) {
            $order[$columnName] = $callback($order);
        }
    }

    /**
     * @param $labelId
     * @param $orderId
     * @return void
     */
    public function removeLabel($labelId, $orderId): void
    {
        $arr = [];
        RemoveLabelService::removeLabels(Order::find($orderId), [$labelId], $arr, [], null);

        $this->reloadDatatable();
    }

    protected function settleNonStandardColumns(string $columnNameWithValues, array $columnDisplayName, array $combination): void
    {
        $this->addNonstandardColumn($columnNameWithValues, function (array $order) use ($columnDisplayName, $combination) {
            $columnDisplayName['data'] = array_merge($columnDisplayName['data'], $combination);
            $class = new $columnDisplayName['class'](['labelGroupName' => $columnDisplayName['data']['name']]);

            return $class($order, $columnDisplayName['data']);
        });
    }

}
