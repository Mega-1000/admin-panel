<?php

namespace App\Jobs;

use App\Entities\ChatAuction;
use App\Facades\Mailer;
use App\Helpers\SMSHelper;
use App\Mail\AuctionFinishedNotification;
use App\Services\Label\AddLabelService;
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
            try {
                if ($auction?->chat->order && !$auction?->chat->order->labels->contains(206)) {
                    $auction->end_info_sent = true;
                    $auction->save();

                    $arr = [];
                    AddLabelService::addLabels($auction?->chat->order, [265], $arr, []);

                    Mailer::create()
                        ->to($auction->chat->order->customer->login)
                        ->send(new AuctionFinishedNotification(
                            $auction,
                        ));

                    SMSHelper::sendSms(
                        576205389,
                        'EPH Polska',
                        'Przetarg na twoim koncie zostal zakonczony zobacz tabele wycen pod: https://admin.mega1000.pl/auctions/' . $auction->id . '/end'
                    );
                }
            } catch (\Exception $exception) {

            }
        }
    }
}
