<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public function chatUser()
    {
        return $this->belongsTo(ChatUser::class)->withTrashed();
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->chatUser->user;
    }

    public function employee()
    {
        return $this->chatUser->employee;
    }

    public function customer()
    {
        return $this->chatUser->customer;
    }
}
