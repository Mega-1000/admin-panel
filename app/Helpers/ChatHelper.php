<?php

namespace App\Helpers;

use App\Entities\Employee;
use App\Entities\Firm;

class ChatHelper
{
    public static function formatChatUsers($users, $userType = MessagesHelper::TYPE_USER)
    {
        return $users->map(function ($user) use ($userType) {
            return self::formatChatUser($user, $userType);
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

    public static function formatChatUser($user, $userType = MessagesHelper::TYPE_USER)
    {
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
    }

    public static function getMessageHelper($message)
    {
        $header = '';
        if ($message->chatUser->customer) {
            $header .=  'Klient ';
            $header .=  self::formatEmailAndPhone($message->chatUser->customer->login,
                $message->chatUser->customer->addresses->first()->phone ?? '');
        } else if ($message->chatUser->employee) {
            $header .=  'Obsługa ';
            $header .= $message->chatUser->employee->firstname . ' ' . $message->chatUser->employee->lastname;
            $header .= self::formatEmailAndPhone($message->chatUser->employee->email, $message->chatUser->employee->phone);
            $header .= self::formatEmployeeRoles($message->chatUser->employee);
            $header .= ':';
        } else if ($message->chatUser->user) {
            $header .=  'Moderator ';
            $header .= $message->chatUser->user->name . ' ' . $message->chatUser->user->fistname . ' ' . $message->chatUser->user->lastname;
            $header .= self::formatEmailAndPhone($message->chatUser->user->email, $message->chatUser->user->phone);
            $header .= ':';
        }
        return $header;
    }

    public static function prepareEmployeeButton($order, $userId, $type, $employee)
    {
        $helper = new MessagesHelper();
        $helper->orderId = $order->id;
        $helper->currentUserId = $userId;
        $helper->currentUserType = $type;
        $helper->employeeId = $employee->id;
        $token = $helper->encrypt();
        return $employee->employeeRoles->where('is_contact_displayed_in_fronted', 1)
            ->map(function ($role) use ($token) {
                $button = [
                    'description' => $role->name,
                    'url' => route('chat.show', ['token' => $token])
                ];
                return $button;
            })->toArray();
    }

    public static function createButtonsArrayForOrder($order, $userId, $type): array
    {
        $orderButtons = [];
        foreach ($order->items as $item) {
            $product = $item->product;
            $firm = Firm::where('symbol', 'like', $product->product_name_supplier)->first();
            if (empty($firm)) {
                continue;
            }
            $buttons = [];
            foreach ($firm->employees as $employee) {
                $button = self::prepareEmployeeButton($order, $userId, $type, $employee);
                $buttons = array_merge($button, $buttons);
                $buttons = collect($buttons)->unique('description')->toArray();
            }
            $key = $product->getProducent();
            $orderButtons[$key] = $buttons;
        }
        return $orderButtons;
    }
}
