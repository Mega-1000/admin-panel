<?php

namespace App\Http\Livewire\Traits;

use App\Services\Label\AddLabelService;

trait WithChecking
{
    public bool $allChecked = false;
    public array $checked = [];
    public string $labelToAdd = '';

    public function initWithChecking(): void
    {
        $this->listeners[] = 'addLabelsForCheckedOrders';
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
    public function checkAll(): void
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
            AddLabelService::addLabels($id, [$labelId], $arr, []);
        }

        $this->emit('reloadDatatable');
    }
}
