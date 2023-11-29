<?php

namespace App\Livewire\Traits\OrderDatatable;

trait WithPageLengthManagement
{
    public int $pageLength;

    /**
     * WithPageLengthManagement extends Livewire component and adds page length management functionality to it
     */
    public function initializeWithPageLengthManagement(): void
    {
        $this->pageLength = session()->get('pageLength', 10);
    }

    public function updatedPageLength(): void
    {
        session()->put('pageLength', $this->pageLength);
    }
}
