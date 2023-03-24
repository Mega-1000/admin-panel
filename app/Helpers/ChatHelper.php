<?php

namespace App\Helpers;

use App\User;
use App\Entities\Firm;
use App\Entities\ChatUser;
use App\Entities\Customer;
use App\Entities\Employee;
use Illuminate\Support\Collection;

class ChatHelper
{
    /**
     * Return many chat user data formatted to array
     *
     * @param  Collection<Employee|Customer|User> $users
     * @param  string $userType
     *
     * @return array[string]
     */
    public static function formatChatUsers(Collection $users, string $userType): array
    {
        return $users->map(function ($user) use($userType) {
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
    /**
     * Return chat user data formatted to string
     *
     * @param  Employee|Customer|User $chatUser
     * @param  string  $userType
     *
     * @return string
     */
    public static function formatChatUser(Employee|Customer|User $chatUser, string $userType): string
    {
        if ($userType == MessagesHelper::TYPE_EMPLOYEE) {
            $firstname = $chatUser->firstname_visibility ? $chatUser->firstname : '';
            $lastname  = $chatUser->lastname_visibility ? $chatUser->lastname : '';
            $phone     = $chatUser->phone_visibility ? $chatUser->phone : '';
            $email     = $chatUser->email_visibility ? $chatUser->email : '';
            $email     = $chatUser->email_visibility ? $chatUser->email : '';
            $roles     = $chatUser->employeeRoles->pluck('name')->join('<br>');
            $userData  = "$roles</br>$firstname $lastname </br>$phone $email";

        } else if ($userType == MessagesHelper::TYPE_CUSTOMER) {
            $emailPhone = self::formatEmailAndPhone($chatUser->login, $chatUser->addresses->first()->phone);
            $userData   = $emailPhone.'<br>'.$chatUser->addresses->first()->postal_code
                          .' '. $chatUser->addresses->first()->city;
        } else if($userType == MessagesHelper::TYPE_USER) {
            $userData = $chatUser->name .' '. $chatUser->firstname
                        .' '. $chatUser->lastname.'<br>'.$chatUser->email.'<br>'. $chatUser->phone;
        }
        return $userData;
    }

    public static function getMessageHeader($message)
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
            if (!$product->product_name_supplier) {
                continue;
            }
            
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
