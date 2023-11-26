<?php

namespace App\Http\Livewire\Traits;

use App\Services\OrderDatatable\OrderDatatableRetrievingService;

trait WithSorting
{
    public array $columns = [];

    public function __construct($id = null)
    {
        $this->columns = OrderDatatableRetrievingService::getColumnNames();

        parent::__construct($id);
    }
}
