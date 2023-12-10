<?php

namespace App\Http\Livewire\Traits;

trait WithChecking
{
    public bool $allChecked = false;
    public array $checked = [];

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
}
