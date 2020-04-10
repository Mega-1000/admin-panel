<?php

namespace App\Entities;

use App\Helpers\ChatHelper;
use Illuminate\Database\Eloquent\Model;

class ChatUser extends Model
{
    protected $table = 'chat_user';

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getUserNicknameForChat()
    {
        $user = $this->customer;
        if (! empty($user)) {
            return '<th class="alert-warning alert"> Klient mail:' . $user->login . '</th>';
        }
        $user = $this->user;
        if (! empty($user)) {
            return '<th class="alert-success alert">' . ChatHelper::formatChatUser($user) . '</th>';
        }
        $user = $this->employee;
        if (! empty($user)) {
            return '<th class="alert-info alert">' . ChatHelper::formatChatUser($user) . '</th>';
        }
    }
}
