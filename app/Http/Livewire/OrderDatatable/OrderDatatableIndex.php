<?php

namespace App\Http\Livewire\OrderDatatable;

use App\Http\Livewire\Traits\WithChecking;
use App\Http\Livewire\Traits\WithGeneralFilters;
use App\Http\Livewire\Traits\WithNonStandardColumnsSorting;
use App\Http\Livewire\Traits\WithFilters;
use App\Http\Livewire\Traits\WithNonstandardColumns;
use App\Services\OrderDatatable\OrderDatatableRetrievingService;
use App\Livewire\Traits\OrderDatatable\WithPageLengthManagement;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Redirector;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class OrderDatatableIndex extends Component
{
    use WithFilters, WithPageLengthManagement, WithColumnsDragAndDrop, WithNonstandardColumns, WithNonStandardColumnsSorting, WithGeneralFilters, WithChecking;

    public array $orders;
    public bool $loading = false;
    public $listeners = ['updateColumnOrderBackend', 'reloadDatatable', 'semiReloadDatatable'];
    public bool $shouldRedirect = false;

    /**
     * OrderDatatableIndex extends Livewire component and adds datatable functionality to it
     *
     * @return View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function render()
    {
        $this->orders = (new OrderDatatableRetrievingService())->getOrders();

        $redirectInstance = $this->reRenderFilters();
        if (!is_null($redirectInstance)) {
            $this->shouldRedirect = true;
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
     * @return mixed
     */
    public function reloadDatatable(): mixed
    {
        return redirect()->route('orders.index', ['applyFiltersFromQuery' => true]);
    }

    public function semiReloadDatatable(): void
    {
        $this->render();
    }
}
