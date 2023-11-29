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

        $this->addNonstandardColumn('labels', function (array $order) {
            return 'okej';
        });
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
