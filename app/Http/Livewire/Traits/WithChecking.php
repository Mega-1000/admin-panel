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

        $this->skipRender();
    }

    /**
     * Check all orders on current page
     */
    public function selectAllOrders(): void
    {
        $this->allChecked = true;

        $ids = collect($this->orders['data'])->pluck('id')->toArray();

        foreach ($ids as &$id) {
            $pattern = '/taskOrder-(\d+)/';

            if (preg_match($pattern, $id, $matches)) {
                $id = $matches[1];
            }
        }

        $this->checked = $ids;
    }

    /**
     * @param int $labelId
     * @return void
     */
    public function addLabelsForCheckedOrders(int $labelId): void
    {
        foreach ($this->checked as $id) {
            $arr = [];
            AddLabelService::addLabels(Order::find($id), [$labelId], $arr, []);
        }

        $this->emit('reloadDatatable');
    }
}
