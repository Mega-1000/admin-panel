<?php

namespace App\Jobs;

use App\Entities\OrderPackage;
use App\Helpers\allegroRestApi\AllegroRestClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AllegroTrackingNumberUpdater implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $packages = OrderPackage::whereHas('order', function ($query) {
            $query->whereNotNull('sello_id')
                ->with('selloTransaction');
        })->where('tracking_number_sent_to_allegro', 0)->get();

        $restClient = new AllegroRestClient();
        $packages->map(function ($package) use ($restClient) {
            $restClient->sendTrackingNumber($package);
        });
    }
}
