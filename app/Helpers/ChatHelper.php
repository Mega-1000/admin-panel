<?php

namespace App\Helpers;

class ChatHelper
{
    public static function formatChatUsers($users)
    {
        return $users->map(function ($user) {
            return self::formatChatUser($user);
        })->toArray();
    }

    public static function formatEmployeeRoles($employee)
    {
        $header = '';
        if ($employee->employeeRoles->count() > 0) {
            $header .= '<br />';
            $header .= '(';
            $header .= implode(', ', $employee->employeeRoles->map(function ($role) {
                return $role->name;
            })->toArray());
            $header .= ')';
        }
        return $header;
    }
    public static function formatEmailAndPhone($email, $phone) {
        $header = '';
        if ($email) {
            $header .= ' &lt' . $email . '&gt';
        }
        if ($phone) {
            $header .= ' tel: ' . $phone;
        }
        return $header;
    }

    public static function formatChatUser($user)
    {
        $ret = [$user->name . ' ' . $user->firstname . ' ' . $user->lastname, $user->email, $user->phone];
        return implode('<br />', $ret);
    }
}
