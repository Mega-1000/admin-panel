<?php

namespace App\Http\Livewire;

use App\Entities\Order;
use Illuminate\View\View;
use Livewire\Component;

class TableOfShipmentPaymentsErrors extends Component
{
    public string $from = '';
    public string $to = '';
    public $orders;
    public bool $showTable = false;

    public function render(): View
    {
        $this->orders = $this->orders ?? collect();
        return view('livewire.table-of-shipment-payments-errors');
    }

    public function searchForOrders(): void
    {
        $this->showTable = true;

        $this->orders = Order::query()->wherehas('packages', function ($query) {
            $query->whereBetween('real_cost_for_company_sum', [(int)$this->from, (int)$this->to]);
        })->get();
    }
}
