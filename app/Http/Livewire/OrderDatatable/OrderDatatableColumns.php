<?php

namespace App\Http\Livewire\OrderDatatable;

use Illuminate\View\View;
use Livewire\Component;
use App\OrderDatatableColumn;
use Livewire\Redirector;

class OrderDatatableColumns extends Component
{
    public array $columns = [];
    public $listeners = ['updateColumnOrder', 'hideColumn'];

    public function render(): View
    {
        return view('livewire.order-datatable.order-datatable-columns');
    }
}
