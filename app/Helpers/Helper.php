<?php
/**
 * Created by PhpStorm.
 * User: Adam Mac
 * Date: 17.12.2018
 * Time: 15:12
 */

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class Helper
{
    const USER_ROLE = [
        '1' => 'super_admin',
        '2' => 'admin',
        '3' => 'accountant',
        '4' => 'consultant',
        '5' => 'storekeeper'
    ];

    public static function checkRole($module, $field)
    {
        $currentUser = Auth::user()->role_id;
        $roleName = self::USER_ROLE[$currentUser];
        if ($roleName !== 'super_admin' && $roleName !== 'admin') {
            $config = config('permissions.' . $roleName . '.' . $module . '.' . $field);
            if ($config) {
                return "true";
            } else {
                return "false";
            }
        } else {
            return true;
        }
    }

    public static function setColumnVisibilityGroup($module, $groupName, $method)
    {
        $currentUser = Auth::user()->role_id;
        $roleName = self::USER_ROLE[$currentUser];

        $config = config('column_visibilities.' . $module . '.' . $groupName . '.' . $roleName . '.' . $method);

        $columns = [];
        foreach ($config as $item) {
            array_push($columns, $item);
        }

        return $columns;
    }

    public static function sendEmail($email, $template, $subject, $additionalData = [])
    {
        if (strpos($email, 'allegromail.pl')) {
            return;
        }
        try {
            return \Mailer::create()->send(
                "emails/$template",
                array_merge(['email' => $email], $additionalData),
                function ($m) use ($email, $subject) {
                    $to = env('MAIL_DEV_ADDRESS', $email);
                    $name = env('MAIL_DEV_NAME', $email);
                    $m->to($to, $name)->subject($subject);
                }
            );
        } catch (\Exception $e) {
            \Log::error('Mail::send', ['message' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
        }
    }

    /**
     * @param $number
     *
     * @return false|string|string[]|null
     *
     */
    public static function preparePhone($number)
    {
        $phone = preg_replace('/[^0-9]/', '', $number);
        $pos = strpos($phone, '48');
        if ($pos === 0) {
            $phone = substr($phone, 2);
        }
        if (strlen($phone) > 7) {
            return $phone;
        }
    }

    public static function phoneIsCorrect($number)
    {
        $number = str_replace(' ', '', $number);
        $number = str_replace('+48', '', $number);

        $len = strlen($number);

        return $len >= 9;
    }

    /**
     * @param $number
     *
     * @return array
     *
     * Numer telefonu - numbers, clear all other symbols. Check symbol count (plus - is 2 symbols).
     * >= 13, then 10 from right is Numer telefonu
     * < 13, then 9 from right is Numer telefonu
     * left symbols are Numer telefonu kierunkowy from righ (old-new).
     * Numer telefonu < 9 including `+` - error.
     */
    public static function prepareCodeAndPhone($number)
    {
        $phone = (string)preg_replace('/^\+?1|\|1|\D/', '', $number);
        $len = strlen($phone);

        $hasPlus = $phone[0] == '+';
        $len += ($hasPlus ? 1 : 0);
        $code = '';

        if ($len >= 13) {
            $code = substr($phone, 0, strlen($phone) - 10);
            $phone = substr($phone, strlen($phone) - 10);
        } elseif ($len >= 9) {
            $code = substr($phone, 0, strlen($phone) - 9);
            $phone = substr($phone, strlen($phone) - 9);
        }
        return [$code, $phone];
    }

    public static function clearSpecialChars($string, $removeDigits = true)
    {
        $string = preg_replace('/[^\ \w$\x{0080}-\x{FFFF}]+/u', '', $string);
        if ($removeDigits) {
            $string = preg_replace('/[0-9]+/', '', $string);
        }
        return $string;
    }
}
