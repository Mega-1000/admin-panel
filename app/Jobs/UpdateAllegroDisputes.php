<?php

namespace App\Jobs;

use App\Services\AllegroDisputeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class UpdateAllegroDisputes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $allegroDisputeService = app(AllegroDisputeService::class);
        $allegroDisputeService->updateOngoingDisputes();
        $allegroDisputeService->unlockInactiveDisputes();
    }
}
