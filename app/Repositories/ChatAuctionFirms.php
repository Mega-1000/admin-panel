<?php

namespace App\Repositories;

use App\Entities\ChatAuctionFirm;
use App\Entities\OrderItem;
use App\Entities\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ChatAuctionFirms
{
    /**
     * @param $token
     * @return Collection
     */
    public static function getItemsByToken($token): array
    {
        $order = ChatAuctionFirm::where('token', $token)->first()
            ->chatAuction
            ->chat
            ->order;

        $items = $order->items;
        $res = [];

        foreach ($items as &$item) {
            $product = $item->product;
            $products = Product::where('product_group', $product->product_group)
                ->where('product_name_supplier', ChatAuctionFirm::where('token', $token)->first()->firm->symbol)
                ->get();

            foreach ($products as $p) {
                $p->quantity = dd(OrderItem::where('order_id', $order->id)->whereHas('product', function ($q) use ($p) {
                    $q->where('product_group', $p->product_group)
                        ->orWhere('parent_id', $p->parent_id);
                }))->sum('quantity');
            }

            $res[] = $products;
        }

        return $res;
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
            ->get();
    }

    public static function getCurrentFirmOffersByToken(string $token)
    {
        return ChatAuctionFirm::query()
            ->where('token', $token)
            ->first()
            ->chatAuction
            ->offers;
    }

    /**
     * Create chat auction firm
     *
     * @param $auction
     * @param $firm
     * @param $emailOfEmplotee
     * @return string
     */
    public function createWithToken($auction, $firm, $emailOfEmplotee): string
    {
        $token = Str::random(60);

        ChatAuctionFirm::create([
            'chat_auction_id' => $auction->id,
            'firm_id' => $firm->id,
            'token' => $token,
            'email_of_employee' => $emailOfEmplotee,
        ]);

        return $token;
    }
}
