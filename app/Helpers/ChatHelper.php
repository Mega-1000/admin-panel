<?php

namespace App\Helpers;

use App\Entities\Employee;

class ChatHelper
{
    public static function formatChatUsers($users, $userType = MessagesHelper::TYPE_USER)
    {
        return $users->map(function ($user) use ($userType) {
            if (is_a($user, Employee::class) && $userType == MessagesHelper::TYPE_CUSTOMER) {
                $ret [] = $user->firstname_visibility ? $user->firstname : '';
                $ret [] = $user->lastname_visibility ? $user->lastname : '';
                $ret = implode(' ', $ret);
                $ret2 [] = $user->phone_visibility ? $user->phone : '';
                $ret2 [] = $user->email_visibility ? $user->email : '';
                $ret2 = implode(' ', $ret2);
                $ret = [$ret, $ret2];
            } else {
                $ret = [$user->name . ' ' . $user->firstname . ' ' . $user->lastname, $user->email, $user->phone];
            }
            return implode('<br />', $ret);
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

    public static function formatEmailAndPhone($email, $phone)
    {
        $header = '';
        if ($email) {
            $header .= ' &lt' . $email . '&gt';
        }
        if ($phone) {
            $header .= ' tel: ' . $phone;
        }
        return $header;
    }
}
