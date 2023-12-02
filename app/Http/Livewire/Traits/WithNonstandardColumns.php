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
        foreach (OrderDatatableColumnsEnum::NON_STANDARD_COLUMNS as $columnName => $columnDisplayName) {
            $this->addNonstandardColumn($columnName, function (array $order) use ($columnDisplayName) {
                $class = new $columnDisplayName['class']();
                return $class($order);
            });
        }

        $this->addNonstandardColumn('nie-wyjechalo', function (array $order) {
            return 'okej1';
        });

        $labelGroupNames = [
            'platnosci' => 'Płatności',
            'produkcja' => 'Produkcja',
            'transport' => 'Transport',
            'info dodatkowe' => 'Info dodatkowe',
            'fakury zakupu' => 'Faktury zakupu',
        ];

        foreach ($labelGroupNames as $labelGroupName => $labelGroupDisplayName) {
            $this->addNonstandardColumn('labels-' . $labelGroupName, function (array $order) use ($labelGroupName, $labelGroupDisplayName) {
                return view('livewire.order-datatable.nonstandard-columns.labels', compact('order', 'labelGroupName', 'labelGroupDisplayName'))->render();
            });
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

        $this->render();
        $this->reloadDatatable();
    }

}
