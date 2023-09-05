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
    public bool $isDeletionConfirmationModalOpen = false;
    public int $deletingPositionId = 0;
    public int $deletingPositionIndex = 0;


    public function __construct($order)
    {
        $this->isDeletionConfirmationModalOpen = false;
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

    public function confirmDeletion(): void
    {
        $this->savePosition($this->deletingPositionId, $this->deletingPositionIndex);
    }

    public function savePosition($itemId, $index): void
    {
        $creationData = $this->creatingPositions[$itemId][$index];

        unset($this->creatingPositions[$itemId][$index]);

        $existingRecord = ProductStockPosition::where('lane', '=', $creationData['lane'])
            ->where('bookstand', '=', $creationData['bookstand'])
            ->where('shelf', '=', $creationData['shelf'])
            ->where('position', '=', $creationData['position'])
            ->with(['stock' => function ($q) {
                $q->with('product');
            }])
            ->first();

        if ($existingRecord->position_quantity !== 0) {
            dd('nie można usunąć pozycji, która ma już ilość');
        }

        if ($existingRecord && !$this->isDeletionConfirmationModalOpen) {
            $this->isDeletionConfirmationModalOpen = true;
            $this->deletingPositionId = $existingRecord->id;
            $this->deletingPositionIndex = $index;
            return;
        }

        if ($existingRecord) {
            $existingRecord->delete();
        }

        $productStock = ProductStockPosition::create($creationData + [
            'product_stock_id' => $this->order->items->find($itemId)->product->stock->id,
        ]);

        ProductStockLog::create([
            'product_stock_id' => $productStock->product_stock_id,
            'product_stock_position_id' => $productStock->id,
            'order_id' => $this->order->id,
            'action' => 'ADD',
            'quantity' => (int)$productStock->position_quantity,
            'user_id' => auth()->user()->id,
        ]);

        $this->order->items->find($itemId)->refresh();
    }

    public function closeModal(): void
    {
        $this->isDeletionConfirmationModalOpen = false;
    }

    public function cancelAdding($itemId, $index): void
    {
        unset($this->creatingPositions[$itemId][$index]);
    }
}
