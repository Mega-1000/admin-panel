<?php

namespace App\Repositories;

use App\Entities\Chat;
use App\Entities\ChatUser;
use Illuminate\Support\Collection;

class Chats
{
    /**
     * Get customer chats by customer id
     *
     * @param  int        $customerId
     *
     * @return Collection $customerChatIds
     */
    public static function getCustomerChats(int $customerId): Collection
    {
        $customerChatIds = ChatUser::where('customer_id', $customerId)->get()->pluck('chat_id');

        return $customerChatIds;
    }

    /**
     * Get contact chats by customer chat ids
     *
     * @param  Collection|array $customerChatIds
     *
     * @return Chat|null        $contactChat
     */
    public static function getContactChats(Collection|array $customerChatIds): ?Chat
    {
        $contactChat = Chat::whereNull(['order_id', 'product_id'])->whereIn('id', $customerChatIds)->first();

        return $contactChat;
    }
}
