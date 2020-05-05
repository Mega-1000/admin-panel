<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'chat_user')->withTimestamps();
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'chat_user')->withTimestamps();
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'chat_user')->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function chatUsers()
    {
        return $this->hasMany(ChatUser::class);
    }

    public function chatUsersWithTrashed()
    {
        return $this->hasMany(ChatUser::class)->withTrashed();
    }

    public function getLastMessage()
    {
        return $this->messages()->orderBy('id', 'desc')->first();
    }
}
