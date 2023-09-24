<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property int $chat_id
 * @property int $chat_user_id
 * @property string $message
 * @property ChatUser $chatUser
 * @property string $created_at
 */
class Message extends Model
{
    protected $fillable = [
        'chat_id',
        'chat_user_id',
        'message',
        'area',
    ];

    public function chatUser()
    {
        return $this->belongsTo(ChatUser::class)->withTrashed();
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): ?User
    {
        return $this->chatUser->user;
    }

    public function employee(): ?Employee
    {
        return $this->chatUser->employee;
    }

    public function customer(): ?Customer
    {
        return $this->chatUser?->customer;
    }
}
