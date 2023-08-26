<?php

namespace App\Http\Livewire;

use AllowDynamicProperties;
use App\Entities\Report;
use App\Entities\ShippingPayInReport;
use Illuminate\View\View;
use Livewire\Component;

#[AllowDynamicProperties] class ShipmentPayInReportDatatable extends Component
{
    public int $reportId;
    public $report;
  
    public string|null $comment;

    public function render(): View
    {
        $this->report = ShippingPayInReport::find($this->reportId);
        $this->comment = $this->report->comments;

        return view('livewire.shipment-pay-in-report-datatable');
    }

    public function saveComments(): void
    {
        $this->report->comments = $this->comment;
        $this->report->save();
    }
}
