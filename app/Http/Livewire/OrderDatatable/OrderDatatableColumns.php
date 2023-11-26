<?php

namespace App\Http\Livewire\OrderDatatable;

use App\Services\OrderDatatable\OrderDatatableRetrievingService;
use Illuminate\View\View;
use Livewire\Component;
use App\OrderDatatableColumns as OrderDatatableColumnsModel;
use Livewire\Redirector;

class OrderDatatableColumns extends Component
{
    public array $columns = [];
    public $listeners = ['updateColumnOrder', 'hideColumn'];
    public string $test = '';

    public function __construct($id = null)
    {
        $this->columns = OrderDatatableRetrievingService::getColumnNames();

        parent::__construct($id);
    }

    public function render(): View
    {
        return view('livewire.order-datatable.order-datatable-columns');
    }

    public function updateColumnOrder($newOrder): Redirector
    {
        $newOrder = collect($newOrder);
        $newOrder = $newOrder->filter(fn ($name) => !empty($name));
        foreach ($this->columns as $key => $column) {
            $this->columns[$key]['order'] = $newOrder->search($column['label']) + 1;
        }

        $this->columns = array_combine(array_column($this->columns, 'order'), $this->columns);
        $this->columns = collect($this->columns)->sortBy('order')->toArray();

        OrderDatatableColumnsModel::all()->each(fn ($column) => $column->delete());
        foreach ($this->columns as $column) {
            OrderDatatableColumnsModel::create([
                'order' => $column['order'],
                'hidden' => false,
                'size' => $column['size'],
                'user_id' => auth()->user()->id,
                'label' => $column['label'],
            ]);
        }

        return redirect(route('orderDatatable'));
    }

    public function hideColumn($name): Redirector
    {
        OrderDatatableColumnsModel::where('label', $name)->update(['hidden' => true]);

        return redirect(route('orderDatatable'));
    }
}
