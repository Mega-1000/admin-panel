<?php

namespace App\Http\Livewire\Traits;

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
        $this->addNonstandardColumn('akcje', function (array $order) {
            return view('livewire.order-datatable.nonstandard-columns.actions', compact('order'))->render();
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

}
