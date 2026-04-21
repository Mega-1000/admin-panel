<?php

namespace App\Jobs;

use App\Entities\ChatAuctionFirm;
use App\Entities\Firm;
use App\Facades\Mailer;
use App\Mail\AuctionNotResponded;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckForAuctionsNotRespondedByFirms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $firms = ChatAuctionFirm::whereDoesntHave('offers')->get()->unique('firm_id');
        foreach ($firms as $firm) {
            $firm = Firm::find($firm->firm_id);

            $amountOfAuctions = ChatAuctionFirm::where('firm_id', $firm->id)->whereDoesntHave('offers')->count();

            Mailer::create()
                ->to($firm->email)
                ->send(new AuctionNotResponded($firm, $amountOfAuctions));

        }
    }
}
