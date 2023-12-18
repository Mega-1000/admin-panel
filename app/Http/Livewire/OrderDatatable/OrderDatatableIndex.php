<?php

namespace App\Http\Livewire\OrderDatatable;

use App\Http\Livewire\Traits\WithChecking;
use App\Http\Livewire\Traits\WithGeneralFilters;
use App\Http\Livewire\Traits\WithNonStandardColumnsSorting;
use App\Http\Livewire\Traits\WithSorting;
use App\Http\Livewire\Traits\WithNonstandardColumns;
use App\Services\OrderDatatable\OrderDatatableRetrievingService;
use App\Livewire\Traits\OrderDatatable\WithPageLengthManagement;
use Illuminate\View\View;
use Livewire\Component;

class OrderDatatableIndex extends Component
{
    use WithSorting, WithPageLengthManagement, WithColumnsDragAndDrop, WithNonstandardColumns, WithNonStandardColumnsSorting, WithGeneralFilters, WithChecking;

    public array $orders;
    public bool $loading = false;
    public $listeners = ['updateColumnOrderBackend', 'reloadDatatable'];

    /**
     * OrderDatatableIndex extends Livewire component and adds datatable functionality to it
     *
     * @return View
     */
    public function render(array $options = []): View
    {
        $this->orders = (new OrderDatatableRetrievingService())->getOrders();

        if ($options['reloadFilters']) {
            $this->reRenderFilters();
        }
        $this->initWithNonstandardColumns();
        $this->initWithNonStandardColumnsSorting();
        $this->initWithGeneralFilters();
        $this->initWithChecking();

        return view('livewire.order-datatable.order-datatable-index');
    }

    /**
     * Listener for event from frontend or child components
     *
     * @return void
     */
    public function reloadDatatable(): void
    {
        redirect()->route('orders.index');
    }

    public function semiReloadDatatable(array $options = []): void
    {
        $this->render($options);
    }
}
