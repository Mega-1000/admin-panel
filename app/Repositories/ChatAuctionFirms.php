<?php

namespace App\Repositories;

use App\Entities\ChatAuctionFirm;
use Illuminate\Support\Collection;

class ChatAuctionFirms
{
    /**
     * Get all products for firm
     *
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
}
