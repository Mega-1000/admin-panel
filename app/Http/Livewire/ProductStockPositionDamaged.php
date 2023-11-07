<?php

namespace App\Http\Livewire;

use App\Entities\ProductStockPosition;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProductStockPositionDamaged extends Component
{
    public int $positionId = 0;
    public int $quantity = 0;
    public bool $saveOnDiffrentPosition = false;
    public string $lane = '';
    public string $shelf = '';
    public string $bookstand = '';
    public string $position = '';
    public string $recentSuccess = '';

    public function render(): View
    {
        return view('livewire.product-stock-position-damaged');
    }

    public function submitForm(): void
    {
        $position = $this->saveOnDiffrentPosition
            ? ProductStockPosition::find($this->positionId)
            : ProductStockPosition::where('lane', $this->lane)
                ->where('shelf', $this->shelf)
                ->where('bookstand', $this->bookstand)
                ->where('position', $this->position)
                ->first();

        $position->update([
            'damaged' => $position->damaged + $this->quantity,
        ]);

        ProductStockPosition::find($this->positionId)->update([
            'position_quantity' => $position->position_quantity - $this->quantity,
        ]);

        $this->recentSuccess = 'Pomyślnie zapisano uszkodzone produkty';
    }
}
