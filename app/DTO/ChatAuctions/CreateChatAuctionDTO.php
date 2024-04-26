<?php

namespace App\DTO\ChatAuctions;

use App\Entities\Chat;

readonly class CreateChatAuctionDTO
{
    public function __construct(
        public Chat $chat,
        public string $end_of_auction,
        public string $date_of_delivery_from,
        public string $date_of_delivery_to,
        public int $price,
        public int $quality,
        public ?string $notes,
    ) {}

    public static function fromRequest(Chat $chat, array $data): self
    {
        return new self(
            $chat,
            $data['end_of_auction'],
            $data['date_of_delivery_from'],
            $data['date_of_delivery_to'],
            $data['price'],
            $data['quality'],
            $data['notes'] ?? '',
        );
    }
}
