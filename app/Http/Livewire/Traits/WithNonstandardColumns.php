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
    public function withNonstandardColumnsInit(): void
    {
        $this->addNonstandardColumn('akcje', function (array $order) {
            return view('livewire.order-datatable.nonstandard-columns.actions', compact('order'))->render();
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
            $order['akcje'] = $callback($order);
        }
    }

}
