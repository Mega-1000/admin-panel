<?php

namespace App\Jobs;

use App\Entities\ConfirmPackages;
use App\Integrations\GLS\GLSClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ConfirmSentPackagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $packagesIdsToConfirm = ConfirmPackages::all();
        $ids = $packagesIdsToConfirm->reduce(function ($acu, $curr) {
            $acu[] = $curr->package->sending_number;
            $curr->delete();
            return $acu;
        }, []);
        $client = new GLSClient();
        $client->auth();
        $client->confirmSending($ids);
        $client->logout();
    }
}
