<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Mail\SpeditionOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendOfferToSpedition implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;


    protected $id;

    protected $email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $email)
    {
        $this->id = $id;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = Order::find($this->id);

        \Mailer::create()
            ->to([$this->email, 'info@mega1000.pl'])
            ->send(new SpeditionOffer("Potwierdzenie oferty dla zamówienia: " . 'YY' . $this->id . 'YY', $order));
    }
}
