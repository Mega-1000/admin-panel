<?php

namespace App\Repositories;

use App\Entities\ChatAuctionFirm;
use Illuminate\Support\Collection;

class ChatAuctionFirms
{
    /**
     * @param $token
     * @return Collection
     */
    public static function getItemsByToken($token): Collection
    {
        return ChatAuctionFirm::where('token', $token)->first()->chatAuction->chat->order->items;
    }

    /**
     * Get auction by token
     *
     * @param $token
     * @return ChatAuctionFirm
     */
    public static function getChatAuctionFirmByToken($token): ChatAuctionFirm
    {
        return ChatAuctionFirm::where('token', $token)->first();
    }

    /**
     * Get firms by chat auction
     *
     * @param $chat_auction_id
     * @return Collection
     */
    public static function getFirmsByChatAuction($chat_auction_id): Collection
    {
        return ChatAuctionFirm::query()->where('chat_auction_id', $chat_auction_id)->distinct('firm_id')->get();
    }
}
