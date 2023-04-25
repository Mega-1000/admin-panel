<?php

namespace App\DTO\ChatAuctions;

readonly class CreateChatAuctionOfferDTO
{
    public function __construct(
        public float $commercial_price_net,
        public float $basic_price_net,
        public float $calculated_price_net,
        public float $aggregate_price_net,
        public float $commercial_price_gross,
        public float $basic_price_gross,
        public float $calculated_price_gross,
        public float $aggregate_price_gross,
        public int $order_item_id,
        public int $chat_auction_id,
        public int $firm_id,
    )
    {
    }

    public static function fromRequest($data): CreateChatAuctionOfferDTO
    {
        return new self(
            $data['commercial_price_net'],
            $data['basic_price_net'],
            $data['calculated_price_net'],
            $data['aggregate_price_net'],
            $data['commercial_price_gross'],
            $data['basic_price_gross'],
            $data['calculated_price_gross'],
            $data['aggregate_price_gross'],
            $data['order_item_id'],
            $data['chat_auction_id'],
            $data['firm_id'],
        );
    }
}
