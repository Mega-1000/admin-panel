<?php

namespace App\Http\Livewire\OrderDatatable;

use App\OrderDatatableColumn;
use Livewire\Redirector;

trait WithColumnsDragAndDrop
{
    public function initializeWithColumnsDragAndDrop(): void
    {
        $this->listeners = [...$this->listeners, 'updateColumnOrder', 'hideColumn'];
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

        OrderDatatableColumn::all()->each(fn ($column) => $column->delete());
        foreach ($this->columns as $column) {
            OrderDatatableColumn::create([
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
        OrderDatatableColumn::where('label', $name)->update(['hidden' => true]);

        return redirect(route('orderDatatable'));
    }
}
