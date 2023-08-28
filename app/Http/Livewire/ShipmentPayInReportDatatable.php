<?php

namespace App\Http\Livewire;

use App\Entities\ShippingPayInReport;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Redirector;

class ShipmentPayInReportDatatable extends Component
{
    public int $reportId;
    public ?ShippingPayInReport $report;
    public ?string $comment;
    public bool $isModalOpen = false;
    public mixed $packageOfferId;
    public string $url = '';

    public function render(): View
    {
        $this->report = ShippingPayInReport::find($this->reportId);
        $this->comment = $this->report->comments;

        return view('livewire.shipment-pay-in-report-datatable');
    }

    public function updatedPackageOfferId(): void
    {
        cookie()->queue('package', json_encode([
            'status' => 'dostarczone',
            'letter_number' => $this->report->numer_listu,
            'shipment_date' => $this->report->data_nadania_otrzymania,
        ]), 60 * 24 * 30);

        $this->url = route('order_packages.create',[
            'id' => $this->packageOfferId,
            'status' => 'dostarczone',
            'letter_number' => $this->report->numer_listu,
            'shipment_date' => $this->report->data_nadania_otrzymania,
        ]);
    }

    public function saveComments(): void
    {
        $this->report->comments = $this->comment;
        $this->report->save();
    }

    public function savePackage(): void
    {
        if (!$this->isModalOpen) {
            $this->isModalOpen = true;
        }
    }
}
