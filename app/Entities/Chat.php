<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_user')->withTimestamps();
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'chat_user')->withTimestamps();
    }

    public function employees():HasMany
    {
        return $this->belongsToMany(Employee::class, 'chat_user')->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function chatUsers(): HasMany
    {
        return $this->hasMany(ChatUser::class);
    }

    public function chatUsersWithTrashed()
    {
        return $this->hasMany(ChatUser::class)->withTrashed();
    }

    public function getLastMessage(): mixed
    {
        return $this->messages()->orderBy('id', 'desc')->first();
    }

    public function auctions(): HasMany
    {
        return $this->hasMany(ChatAuction::class);
    }
}
