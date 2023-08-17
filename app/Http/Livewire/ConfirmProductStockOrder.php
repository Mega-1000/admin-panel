<?php

namespace App\Http\Livewire;

use App\Entities\OrderItem;
use App\Entities\ProductStockLog;
use App\Entities\ProductStockPosition;
use Illuminate\View\View;
use Livewire\Component;

class ConfirmProductStockOrder extends Component
{
    public mixed $order;
    public array $creatingPositions = [];

    public function __construct($order)
    {
        $this->order = $order;

        parent::__construct();
    }

    public function render(): View
    {
        return view('livewire.confirm-product-stock-order');
    }

    public function addPosition(OrderItem $item): void
    {
        $this->creatingPositions[$item->id][] = [
            'position_quantity' => '',
            'lane' => '',
            'shelf' => '',
            'bookstand' => '',
            'position' => '',
        ];
    }

    public function savePosition($itemId, $index): void
    {
        $productStock = ProductStockPosition::create($this->creatingPositions[$itemId][$index] + [
            'product_stock_id' => $this->order->items->find($itemId)->product->stock->id,
        ]);

        $this->order->items->find($itemId)->refresh();

        ProductStockLog::create([
            'product_stock_id' => $productStock->product_stock_id,
            'product_stock_position_id' => $productStock->id,
            'order_id' => $this->order->id,
            'action' => 'ADD',
            'quantity' => $productStock->position_quantity,
            'user_id' => auth()->user()->id,
        ]);
    }

    public function cancelAdding($itemId, $index): void
    {
        unset($this->creatingPositions[$itemId][$index]);
    }
}
