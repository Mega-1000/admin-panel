<?php

namespace App\Http\Livewire;

use App\OrderDatatableColumn;
use Illuminate\View\View;
use Livewire\Component;

class OrderDatatableColumnsManagement extends Component
{
    public array $columns = [];

    public function render(): View
    {
        $this->columns = OrderDatatableColumn::all()->toArray();
        $this->columns = array_map(function ($column) {
            $column['hidden'] = !$column['hidden'];

            return $column;
        }, $this->columns);

        return view('livewire.order-datatable-columns-managment');
    }

    public function updatedColumns(): void
    {
        foreach ($this->columns as $column) {
            OrderDatatableColumn::find($column['id'])->update([
                'hidden' => !$column['hidden'],
            ]);
        }
    }
}
