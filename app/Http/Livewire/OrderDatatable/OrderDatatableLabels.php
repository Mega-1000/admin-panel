<?php

namespace App\Http\Livewire\OrderDatatable;

use App\Entities\Order;
use App\Services\Label\RemoveLabelService;
use Illuminate\View\View;
use Livewire\Component;

class OrderDatatableLabels extends Component
{
    public $order;
    public $labelGroupName;

    public function render(): ?View
    {
        return view('livewire.order-datatable.order-datatable-labels');
    }

}
