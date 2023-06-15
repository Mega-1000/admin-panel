<?php

namespace App\Services;

use App\DTO\ChatAuctions\CreateChatAuctionOfferDTO;
use App\Entities\ChatAuction;
use App\Entities\ChatAuctionOffer;
use App\Jobs\SendNotificationsForAuctionOfferForFirmsJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

class ChatAuctionOfferService
{
    use Dispatchable;

    /**
     * Create offer for auction
     *
     * @param CreateChatAuctionOfferDTO $data
     * @return ChatAuctionOffer
     */
    public function createOffer(CreateChatAuctionOfferDTO $data): ChatAuctionOffer
    {
        $auction = ChatAuction::query()->findOrFail($data->chat_auction_id);

        $chatAuctionOffer = ChatAuctionOffer::create([
            'chat_auction_id' => $auction->id,
            'commercial_price_net' => $data->commercial_price_net,
            'basic_price_net' => $data->basic_price_net,
            'calculated_price_net' => $data->calculated_price_net,
            'aggregate_price_net' => $data->aggregate_price_net,
            'commercial_price_gross' => $data->commercial_price_gross,
            'basic_price_gross' => $data->basic_price_gross,
            'calculated_price_gross' => $data->calculated_price_gross,
            'aggregate_price_gross' => $data->aggregate_price_gross,
            'order_item_id' => $data->order_item_id,
            'firm_id' => $data->firm_id,
            'send_notification' => $data->send_notification,
        ]);

        dispatch_now(new SendNotificationsForAuctionOfferForFirmsJob($chatAuctionOffer));

        return $chatAuctionOffer;
    }
}
