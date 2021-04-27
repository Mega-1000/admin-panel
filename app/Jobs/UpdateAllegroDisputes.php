<?php

namespace App\Jobs;

use App\Services\AllegroDisputeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAllegroDisputes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $allegroDisputeService;

    public function __construct(AllegroDisputeService $service)
    {
        $this->allegroDisputeService = $service;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->allegroDisputeService->updateOngoingDisputes();
    }
}
