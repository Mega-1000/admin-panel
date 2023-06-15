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
            ->join('firms', 'chat_auction_offers.firm_id', '=', 'firms.id') // adjust the columns based on your database structure
            ->where('send_notification', true)
            ->where('chat_auction_id', $chatAuctionOffer->chat_auction_id)
            ->where('order_item_id', $chatAuctionOffer->order_item_id)
            ->select('chat_auction_offers.*', 'firms.email') // adjust the select based on your needs
            ->groupBy('firms.email')
            ->where('commercial_price_net', '>', $chatAuctionOffer->commercial_price_net)
            ->get();
    }


}
