<?php

namespace App\Http\Livewire;

use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;
use App\Services\ProductStockPositionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Livewire\Component;

class AddNewProductStockPositionForm extends Component
{
    public string $position_quantity;
    public int $productStockId;
    public string $lane;
    public string $bookstand;
    public string $shelf;
    public string $position;
    public bool $isDeletionConfirmationModalOpen = false;


    public function __construct($id = null)
    {
        $this->isDeletionConfirmationModalOpen = false;

        parent::__construct($id);
    }

    public function render(): View
    {
        return view('livewire.add-new-product-stock-position-form');
    }

    public function submitForm(): RedirectResponse|null
    {
        $data = [
            'position_quantity' => $this->position_quantity,
            'product_stock_id' => $this->productStockId,
            'lane' => $this->lane,
            'bookstand' => $this->bookstand,
            'shelf' => $this->shelf,
            'position' => $this->position,
        ];

        $existingRecord = ProductStockPosition::where('lane', '=', $data['lane'])
            ->where('bookstand', '=', $data['bookstand'])
            ->where('shelf', '=', $data['shelf'])
            ->where('position', '=', $data['position'])
            ->with(['stock' => function ($q) {
                $q->with('product');
            }])
            ->first();

        if (!empty($existingRecord) && !$this->isDeletionConfirmationModalOpen) {
            $this->isDeletionConfirmationModalOpen = true;
            return null;
        }

        if (!empty($existingRecord)) {
            $existingRecord->delete();
        }

        ProductStockPosition::create(
            array_merge(['product_stock_id' => $this->productStockId], $data)
        );

        return redirect()->route('product_stocks.edit', ['id' => $this->productStockId, 'tab' => 'positions'])->with([
            'message' => __('product_stock_positions.message.store'),
            'alert-type' => 'success'
        ]);
    }

    public function closeModal(): void
    {
        $this->isDeletionConfirmationModalOpen = false;
    }
}
