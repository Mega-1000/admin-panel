<?php

namespace App\Http\Livewire;

use App\Entities\LowOrderQuantityAlert;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Redirector;

class LowOrderQuantityAlertManagement extends Component
{
    public string $itemNames = '';
    public string $minQuantity = '0';
    public int $alertId = 0;
    public ?string $phpCode = '';
    public string $title = '';
    public array $messages = [
        [
            'title' => '',
            'message' => '',
            'delay_time' => 0,
        ],
    ];
    public bool $beenRendered = false;

    public function render(): View
    {
        $lowQuantityAlert = LowOrderQuantityAlert::find($this->alertId);

        if ($lowQuantityAlert) {
            $this->messages = empty($this->messages) || !$this->beenRendered ? $lowQuantityAlert->messages->toArray() : $this->messages;
            $this->title = empty($this->title) ? $lowQuantityAlert->title : $this->title;
            $this->itemNames = empty($this->itemNames) ? $lowQuantityAlert->item_names : $this->itemNames;
            $this->phpCode = empty($this->phpCode) ? $lowQuantityAlert->php_code : $this->phpCode;
            $this->minQuantity = $this->minQuantity == 0 ? $lowQuantityAlert->min_quantity : $this->minQuantity;
        }

        $this->beenRendered = true;
        return view('livewire.low-order-quantity-alert-management');
    }

    public function submitForm(): Redirector
    {
        $this->validate([
            'title' => 'required',
            'itemNames' => 'required',
            'minQuantity' => 'required|numeric',
            'phpCode' => 'required',
        ]);

        $data = [
            'title' => $this->title,
            'item_names' => $this->itemNames,
            'min_quantity' => $this->minQuantity,
            'php_code' => $this->phpCode,
        ];

        if ($alert = LowOrderQuantityAlert::find($this->alertId)) {
            LowOrderQuantityAlert::find($this->alertId)->update($data);
        } else {
            $alert = LowOrderQuantityAlert::create($data);
        }

        $alert->messages()->delete();
        foreach ($this->messages as $message) {
            $alert->messages()->create([
                'title' => $message['title'],
                'message' => $message['message'],
                'delay_time' => $message['delay_time'],
            ]);
        }

        return redirect()->route('low-quantity-alerts.index')
            ->with(['message' => 'Stworzono alert o niskiej ilości produktów',
                'alert-type' => 'success']);
    }

    public function addMessage(): void
    {
        $this->messages[] = [
            'title' => '',
            'message' => '',
            'delay_time' => 0,
        ];
    }
}
