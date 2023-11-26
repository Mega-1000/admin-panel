<?php

namespace App\Http\Livewire\OrderDatatable;

use App\Http\Livewire\Traits\WithSorting;
use App\Services\OrderDatatable\OrderDatatableRetrievingService;
use Illuminate\View\View;
use Livewire\Component;

class OrderDatatableIndex extends Component
{
    use WithSorting;

    public array $orders;
    public $listeners = ['updateColumnOrderBackend'];

    public function render(): View
    {
        $service = app(OrderDatatableRetrievingService::class);
        $this->orders = $service->getOrders();

        return view('livewire.order-datatable.order-datatable-index');
    }

}
