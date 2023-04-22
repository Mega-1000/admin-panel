<?php

namespace App\DTO\ChatAuctions;

use App\Entities\Chat;

class CreateChatAuctionDTO
{
    public function __construct(
        public Chat $chat,
        public string $end_of_auction,
        public string $date_of_delivery,
        public int $price,
        public int $quality,
    ) {
    }

    public static function fromRequest(Chat $chat, array $data): self
    {
        return new self(
            $chat,
            $data['end_of_auction'],
            $data['date_of_delivery'],
            $data['price'],
            $data['quality'],
        );
    }
}
