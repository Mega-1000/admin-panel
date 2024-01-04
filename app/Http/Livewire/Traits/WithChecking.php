<?php

namespace App\Http\Livewire\Traits;

use App\Entities\Order;
use App\Services\Label\AddLabelService;
use Exception;

trait WithChecking
{
    public bool $allChecked = false;
    public array $checked = [];

    public function initWithChecking(): void
    {
        $this->listeners[] = 'addLabelsForCheckedOrders';
        $this->listeners[] = 'selectAllOrders';
    }

    /**
     * Add or remove an order ID from the checked array.
     *
     * @param int $id
     */
    public function checkOrder(int $id): void
    {
        if (!in_array($id, $this->checked)) {
            $this->checked[] = $id;
        } else {
            $this->checked = array_diff($this->checked, [$id]);
        }

        $this->skipRender();
    }

    /**
     * Check all orders on the current page.
     */
    public function selectAllOrders(): void
    {
        $this->allChecked = true;
        $ids = [];
        $array = collect($this->orders['data'])
            ->pluck('id')
            ->toArray();

        foreach ($array as $htmlString) {
            if (preg_match('/taskOrder-(\d+)/', $htmlString, $matches)) {
                $ids[] = $matches[1];
            }
        }

        $this->checked = $ids;

        $this->skipRender();
    }

    /**
     * Add labels to checked orders.
     *
     * @param int $labelId
     * @throws Exception
     */
    public function addLabelsForCheckedOrders(int $labelId): void
    {
        foreach ($this->checked as $id) {
            $arr = [];
            AddLabelService::addLabels(Order::find($id), [$labelId], $arr, []);
        }

        $this->reloadDatatable();
    }
}
