<?php

namespace App\Http\Livewire\Traits;

use App\Entities\Order;
use App\Services\Label\AddLabelService;

trait WithChecking
{
    public bool $allChecked = false;
    public array $checked = [];
    public string $labelToAdd = '';

    public function initWithChecking(): void
    {
        $this->listeners[] = 'addLabelsForCheckedOrders';
        $this->listeners[] = 'selectAllOrders';
    }

    /**
     * @param int $id
     */
    public function checkOrder(int $id): void
    {
        $this->checked[] = $id;
    }

    /**
     * Check all orders on current page
     */
    public function selectAllOrders(): void
    {
        $this->allChecked = true;

        $this->checked = collect($this->orders['data'])->pluck('id')->toArray();
    }

    /**
     * @param int $labelId
     * @return void
     */
    public function addLabelsForCheckedOrders(int $labelId): void
    {
        $arr = [];
        foreach ($this->checked as $id) {
            AddLabelService::addLabels(Order::find($id), [$labelId], $arr, []);
        }

        $this->emit('reloadDatatable');
    }
}
