<?php

namespace App\Http\Livewire\OrderDatatable;

use App\Http\Livewire\Traits\WithSorting;
use App\Http\Livewire\Traits\WithNonstandardColumns;
use App\Services\OrderDatatable\OrderDatatableRetrievingService;
use App\Livewire\Traits\OrderDatatable\WithPageLengthManagement;
use Illuminate\View\View;
use Livewire\Component;

class OrderDatatableIndex extends Component
{
    use WithSorting, WithNonstandardColumns, WithPageLengthManagement, WithColumnsDragAndDrop;

    public array $orders;
    public $listeners = ['updateColumnOrderBackend'];


    public function render(): View
    {
        $this->orders = app(OrderDatatableRetrievingService::class)->getOrders();

        $this->reRenderFilters();
        $this->withNonstandardColumnsInit();
        $this->initializeWithPageLengthManagement();
        $this->initializeWithSorting();
        $this->initializeWithColumnsDragAndDrop();

        return view('livewire.order-datatable.order-datatable-index');
    }

}
