<?php

namespace App\Jobs;

use App\Entities\ChatAuction;
use App\Facades\Mailer;
use App\Mail\AuctionFinishedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckForFinishedAuctions implements ShouldQueue
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
    public function handle(): void
    {
        $auctions = ChatAuction::where('id', '>', 189)->where('end_of_auction', '<', now())->where('end_info_sent', false)->get();

        foreach ($auctions as $auction) {
            Mailer::create()
                ->to($auction->chat->order->customer->login)
                ->send(new AuctionFinishedNotification(
                    $auction,
                ));
        }
    }
}
