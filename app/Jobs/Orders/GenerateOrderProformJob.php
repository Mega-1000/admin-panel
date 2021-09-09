<?php

namespace App\Jobs\Orders;

use App\Entities\Order;
use App\Jobs\Job;
use App\Jobs\OrderProformSendMailJob;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateOrderProformJob extends Job
{
	protected $order;
	protected $sendToCustomer;

	/**
	 * GenerateOrderProformJob constructor.
	 * @param $order
	 */
	public function __construct(Order $order, $sendToCustomer = false)
	{
		$this->order = $order;
		$this->sendToCustomer = $sendToCustomer;
	}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
	    $proformDate = Carbon::now()->format('m/Y');
	    $date = Carbon::now()->toDateString();
	    if ($this->order->proforma_filename && Storage::disk('local')->exists($this->order->proformStoragePath)) {
		    Storage::disk('local')->delete($this->order->proformStoragePath);
	    }
	    $this->order->proforma_filename = Str::random(40) . '.pdf';
	    $this->order->save();
	    
	    $order = $this->order;
	    
	    $pdf = PDF::loadView('pdf.proform', compact('date', 'proformDate', 'order'))->output();
	    Storage::disk('local')->put($this->order->proformStoragePath, $pdf);
	    
	    if ($this->sendToCustomer) {
	    	dispatch(new OrderProformSendMailJob($this->order));
	    }
    }
}
