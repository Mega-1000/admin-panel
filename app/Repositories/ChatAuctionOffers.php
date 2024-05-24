<?php

namespace App\Repositories;

use App\Entities\ChatAuctionOffer;
use FontLib\TrueType\Collection;
use Illuminate\Database\Eloquent\Model;

class ChatAuctionOffers
{
    /**
     * @param string $v
     * @return Model
     */
    public function getOfferByName(string $v): Model
    {
        return ChatAuctionOffer::query()->whereHas('orderItem', function ($query) use ($v) {
            $query->whereHas('product', function ($query) use ($v) {
                $query->where('name', $v);
            });
        })
        ->first();
    }

    /**
     * @param object $v
     * @return Model
     */
    public function getOfferByOrderItem(object $v): Model
    {
        return ChatAuctionOffer::query()->whereHas('orderItem', function ($query) use ($v) {
            $query->whereHas('product', function ($query) use ($v) {
                $query->where('name', $v->product->name);
            });
        })
        ->first();
    }

    /**
     *
     *
     * @param ChatAuctionOffer $chatAuctionOffer
     * @return \Illuminate\Support\Collection
     */
    public static function getFirmsForAuctionOfferForEmailRemider(ChatAuctionOffer $chatAuctionOffer): \Illuminate\Support\Collection
    {
        return ChatAuctionOffer::query()
            ->join('firms', 'chat_auction_offers.firm_id', '=', 'firms.id')
            ->where('send_notification', true)
            ->where('chat_auction_id', $chatAuctionOffer->chat_auction_id)
            ->whereHas('product', function ($q) use ($chatAuctionOffer) {
                $q->where('product_group', $chatAuctionOffer->product->product_group)
                    ->where('additional_info1', $chatAuctionOffer->product->additional_info1);
            })
            ->select('chat_auction_offers.*', 'firms.email')
            ->groupBy('firms.email')
            ->where('commercial_price_net', '<', $chatAuctionOffer->commercial_price_net)
            ->where('firm_id', '!=', $chatAuctionOffer->firm_id)
            ->get();
    }


}
