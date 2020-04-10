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
        $user = $this->customer()->get()->first();
        if (! empty($user)) {
            return '<th class="alert-warning alert"> Klient tel:' . $user->login . '</th>';
        }
        $user = $this->user()->get();
        if (! empty($user->first())) {
            return '<th class="alert-success alert">' . implode(' ', ChatHelper::formatChatUsers($user)) . '</th>';
        }
        $user = $this->employee()->get();
        if (! empty($user)) {
            return '<th class="alert-info alert">' . implode(' ', ChatHelper::formatChatUsers($user)) . '</th>';
        }
    }
}
