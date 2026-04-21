<?php

namespace App\Http\Livewire;

use App\Entities\LowOrderQuantityAlert;
use App\Entities\LowOrderQuantityAlertMessage;
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
    public string $columnName = '';
    public ?string $area = '';
    public array $messages = [
        [
            'title' => '',
            'message' => '',
            'delay_time' => 0,
            'attachment_name' => '',
            'label_id' => '',
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
            $this->columnName = empty($this->columnName) ? $lowQuantityAlert->column_name : $this->columnName;
            $this->area = empty($this->area) ? $lowQuantityAlert->space : $this->area;
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
        ]);

        $data = [
            'title' => $this->title,
            'item_names' => $this->itemNames,
            'min_quantity' => $this->minQuantity,
            'php_code' => $this->phpCode,
            'column_name' => $this->columnName,
            'space' => $this->area,
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
                'attachment_name' => $message['attachment_name'],
                'label_id' => $message['label_id'] ?? '',
            ]);
        }

        return redirect()->route('low-quantity-alerts.index')
            ->with(['message' => 'Stworzono alert o niskiej ilości produktów',
                'alert-type' => 'success']);
    }

    public function deleteMessage(int $key): void
    {
        $message = LowOrderQuantityAlertMessage::where('title', $this->messages[$key]['title'])
            ->where('message', $this->messages[$key]['message'])
            ->where('delay_time', $this->messages[$key]['delay_time'])
            ->first();

        $this->messages = array_filter($this->messages, fn (array $message) => $message['title'] !== $this->messages[$key]['title']);

        $message->delete();
    }

    public function addMessage(): void
    {
        $this->messages[] = [
            'title' => '',
            'message' => '',
            'delay_time' => 0,
            'attachment_name' => '',
            'label_id' => '',
        ];
    }

    public function updateMessage(int $key, $k): void
    {
        $message = LowOrderQuantityAlertMessage::find($key);

        $message->update([
            'title' => $this->messages[$k]['title'],
            'message' => $this->messages[$k]['message'],
            'delay_time' => $this->messages[$k]['delay_time'],
            'attachment_name' => $this->messages[$k]['attachment_name'],
            'label_id' => $this->messages[$k]['label_id'] ?? '',
        ]);
    }
}
