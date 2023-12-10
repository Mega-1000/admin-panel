<?php

namespace App\Http\Livewire\Traits;

use App\Services\Label\AddLabelService;

trait WithChecking
{
    public bool $allChecked = false;
    public array $checked = [];
    public string $labelToAdd = '';

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

    public function addLabelsForCheckedOrders(): void
    {
        $this->validate([
            'labelToAdd' => 'required|string|max:255',
        ]);

        $arr = [];
        foreach ($this->checked as $id) {
            AddLabelService::addLabels($id, [$this->labelToAdd], $arr, []);
        }

        $this->emit('reloadDatatable');
    }
}
