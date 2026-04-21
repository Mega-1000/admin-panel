<?php

namespace App\DTO\ChatAuctions;

use App\Entities\Chat;

readonly class CreateChatAuctionDTO
{
    public function __construct(
        public Chat $chat,
        public string $end_of_auction,
        public int $price,
        public int $quality,
        public ?string $notes,
    ) {}

    public static function fromRequest(Chat $chat, array $data): self
    {
        return new self(
            $chat,
            $data['end_of_auction'],
            $data['price'],
            $data['quality'],
            $data['notes'] ?? '',
        );
    }
}
