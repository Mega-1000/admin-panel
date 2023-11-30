<?php

namespace App\Http\Livewire\OrderDatatable;

use App\Http\Livewire\Traits\WithNonStandardColumnsSorting;
use App\Http\Livewire\Traits\WithSorting;
use App\Http\Livewire\Traits\WithNonstandardColumns;
use App\OrderDatatableColumn;
use App\Services\OrderDatatable\OrderDatatableRetrievingService;
use App\Livewire\Traits\OrderDatatable\WithPageLengthManagement;
use Illuminate\View\View;
use Livewire\Component;

class OrderDatatableIndex extends Component
{
    use WithSorting, WithPageLengthManagement, WithColumnsDragAndDrop, WithNonstandardColumns, WithNonStandardColumnsSorting;

    public array $orders;
    public $listeners = ['updateColumnOrderBackend', 'reloadDatatable'];

    /**
     * OrderDatatableIndex extends Livewire component and adds datatable functionality to it
     *
     * @return View
     */
    public function render(): View
    {
        $this->orders = app(OrderDatatableRetrievingService::class)->getOrders();

        $this->reRenderFilters();
        $this->initWithNonstandardColumns();
        $this->initWithNonStandardColumnsSorting();

        return view('livewire.order-datatable.order-datatable-index');
    }

    /**
     * Listener for event from frontend or child components
     *
     * @return void
     */
    public function reloadDatatable(): void
    {
        $this->render();
    }
}
