<?php

namespace App\Helpers;

class ChatHelper
{
    public static function formatChatUsers($users)
    {
        return $users->map(function ($user) {
            $ret = [$user->name, $user->firstname, $user->lastname, $user->email, $user->phone];
            return implode(' ', $ret);
        })->toArray();
    }
}
