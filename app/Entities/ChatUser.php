<?php

namespace App\Entities;

use App\Entities\interfaces\iChatNickname;
use App\Helpers\ChatHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatUser extends Model implements iChatNickname
{
    use SoftDeletes;
    protected $table = 'chat_user';

    protected $softDelete = true;


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

    public function getUserNicknameForChat($userType)
    {
        $user = $this->customer;
        if (! empty($user)) {
            $display = '<th class="alert-warning alert"> Klient ';
            $display .= ChatHelper::formatChatUser($user). '</th>';
            return $display;
        }
        $user = $this->user;
        if (! empty($user)) {
            return '<th class="bg-primary alert">' . ChatHelper::formatChatUser($user, $userType) . '</th>';
        }
        $user = $this->employee;
        if (! empty($user)) {
            return '<th class="alert-info alert">' . ChatHelper::formatChatUser($user, $userType) . '</th>';
        }
    }
}
