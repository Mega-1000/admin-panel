<?php

namespace App\Repositories;

use App\Entities\ChatAuctionFirm;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
        return ChatAuctionFirm::query()
            ->where('chat_auction_id', $chat_auction_id)
            ->with('firm')
            ->get()
            ->where(function ($q) {
                return $q->chatAuctionOffers->count() !== 0;
            });// distinc by firm email;
    }

    /**
     * Create chat auction firm
     *
     * @param $auction
     * @param $firm
     * @return string
     */
    public function createWithToken($auction, $firm): string
    {
        $token = Str::random(60);

        ChatAuctionFirm::create([
            'chat_auction_id' => $auction->id,
            'firm_id' => $firm->id,
            'token' => $token,
        ]);

        return $token;
    }
}
