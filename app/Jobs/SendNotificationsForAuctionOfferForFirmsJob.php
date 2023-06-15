<?php

namespace App\Jobs;

use App\Entities\ChatAuctionOffer;
use App\Facades\Mailer;
use App\Mail\NotificationsForAuctionOfferForFirmsMail;
use App\Repositories\ChatAuctionOffers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationsForAuctionOfferForFirmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private readonly ChatAuctionOffer $chatAuctionOffer,
    ) {}

    /**
     * Execute the job.
     *
     * @param ChatAuctionOffers $chatAuctionOfferRepository
     * @return void
     */
    public function handle(ChatAuctionOffers $chatAuctionOfferRepository): void
    {
        $firms = $chatAuctionOfferRepository->getFirmsForAuctionOfferForEmailRemider($this->chatAuctionOffer);

        foreach ($firms as $firm) {
            $this->sendMailToFirm($firm->email);
        }
    }

    /**
     * Send mail to firm
     *
     * @param string $email
     * @return void
     */
    private function sendMailToFirm(string $email): void
    {
        Mailer::create()
            ->to($email)
            ->send(new NotificationsForAuctionOfferForFirmsMail($this->chatAuctionOffer, $email));
    }
}
