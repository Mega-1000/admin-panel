<?php

namespace App\Jobs\Orders;

use App\Entities\Order;
use App\Jobs\Job;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateOrderProformJob extends Job
{
    protected $order;
    protected $regenerate;

    /**
     * GenerateOrderProformJob constructor.
     * @param $order
     */
    public function __construct(Order $order, $regenerate = false)
    {
        $this->order = $order;
        $this->regenerate = $regenerate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $exists = $this->order->proforma_filename && Storage::disk('local')->exists($this->order->proformStoragePath);

        if ($this->regenerate && $exists) {
            Storage::disk('local')->delete($this->order->proformStoragePath);
            $exists = false;
        }

        if ($exists) {
            return;
        }

        $proformDate = Carbon::now()->format('m/Y');
        $date = Carbon::now()->toDateString();

        $this->order->proforma_filename = Str::random(40) . '.pdf';
        $this->order->save();

        $order = $this->order;

        $pdf = Pdf::loadView('pdf.proform', compact('date', 'proformDate', 'order'))->output();
        Storage::disk('local')->put($this->order->proformStoragePath, $pdf);
    }
}
