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
        return ChatUser::where('customer_id', $customerId)->get()->pluck('chat_id');
    }

    /**
     * Get contact chats by customer chat ids
     *
     * @param  Collection|array $customerChatIds
     *
     * @return Chat|null        $contactChat
     */
    public static function getContactChat(Collection|array $customerChatIds): ?Chat
    {
        return Chat::whereNull(['order_id', 'product_id'])->whereIn('id', $customerChatIds)->first();
    }
    /**
     * Get contact chats with need_intervention set to true, and where user == current auth user
     *
     * @return Collection $contactChat
     */
    public static function getChatsNeedIntervention(): Collection
    {
        return Chat::where('need_intervention', true)->whereNull(['product_id', 'order_id', 'user_id'])->get();
    }
    /**
     * Get blank user, can only be one blank user per chat
     *
     * @param  Collection    $chatUsers
     *
     * @return ChatUser|null $blankChatUser
     */
    public static function getBlankChatUser(Collection $chatUsers): ?ChatUser {
        return $chatUsers->whereNull('user_id')->whereNull('customer_id')->whereNull('employee_id')->first();
    }

    /**
     * Get full chat object including messages, chat users, and order
     *
     * @param int $chatId
     * @return Chat
     */
    public static function getFullChatObject(int $chatId): Chat
    {
        return Chat::with(['messages' => function ($q) {
            $q->with(['chatUser' => function ($q) {
                $q->with(['customer' => function ($q) {
                    $q->with(['addresses' => function ($q) {
                        $q->whereNotNull('phone');
                    }]);
                }]);
                $q->with('user');
                $q->with('employee');
            }]);
            $q->oldest();
        }])
        ->with('order')
        ->find($chatId);
    }
}
