<?php

namespace App\Http\Livewire\Orders;

use App\Entities\LabelGroup;
use App\OrderDatatableColumn;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

class LabelSearch extends Component
{
    public string $groupName;
    public array $labels = [];
    public bool $showContainer = false;
    protected $listeners = ['localStorageDataUpdated'];
    public ?array $localStorageData;
    public string $sessionName = '';

    public function localStorageDataUpdated(string $data): void
    {
        $this->localStorageData = explode(',', $data);
    }

    public function render(): View
    {
        return view('livewire.orders.label-search');
    }


    public function toggleContainer()
    {
        if (!$this->showContainer) {
            $this->getAssociatedLabelsToOrderFromGroup();
        }

        $this->showContainer = !$this->showContainer;
    }

    public function getAssociatedLabelsToOrderFromGroup(): array
    {
        $groupId = LabelGroup::where('name', str_replace('labels-', '', $this->groupName))->first()->id;

        $labels = DB::table('labels')
            ->distinct()
            ->select('labels.*')
            ->rightJoin('order_labels', 'order_labels.label_id', '=', 'labels.id')
            ->where(['labels.label_group_id' => $groupId])
            ->get();

        return $this->labels = $labels->toArray();
    }

    public function selectCurrent($id)
    {
        OrderDatatableColumn::where('label', $this->groupName)->update(['filter' => $id]);

        $this->toggleContainer();

        $this->emit('reloadDatatable');
    }

    public function clearSelected()
    {
        OrderDatatableColumn::where('label', $this->sessionName)->update(['filter' => '']);

        $this->emit('reloadDatatable');

        $this->toggleContainer();
    }
}
