<?php
/**
 * Created by PhpStorm.
 * User: Adam Mac
 * Date: 17.12.2018
 * Time: 15:12
 */

namespace App\Helpers;

use App\Facades\Mailer;
use Exception;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Helper
{
    const USER_ROLE = [
        '1' => 'super_admin',
        '2' => 'admin',
        '3' => 'accountant',
        '4' => 'consultant',
        '5' => 'storekeeper'
    ];

    public static function checkRole($module, $field): true|string
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

    public static function sendEmail($email, $template, $subject, $additionalData = []): ?SentMessage
    {
        try {
            return Mailer::create()->send(
                "emails/$template",
                array_merge(['email' => $email], $additionalData),
                function ($m) use ($email, $subject) {
                    $to = $email;
                    $name = $email;
                    $m->to($to, $name)->subject($subject);
                }
            );
        } catch (Exception $e) {
            Log::error('Mail::send', ['message' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
        }

        return null;
    }

    /**
     * @param $number
     *
     * @return string
     */
    public static function preparePhone($number): string
    {
        $phone = preg_replace('/[^0-9]/', '', $number);
        $pos = strpos($phone, '48');
        if ($pos === 0) {
            $phone = substr($phone, 2);
        }

        return strlen($phone) > 7 ? $phone : '';
    }

    public static function phoneIsCorrect($number): bool
    {
        // Usuń spacje, myślniki i inne znaki specjalne z numeru telefonu
        $number = preg_replace('/\D/', '', $number);
        $number = str_replace('+48', '', $number);

        // Sprawdź, czy numer telefonu składa się tylko z cyfr 
        if (!preg_match('/^\d+$/', $number))
        {
            return false;
        }

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

    public static function clearSpecialChars($string, $removeDigits = true): array|string|null
    {
        $string = preg_replace('/[^\s\w$\x{0080}-\x{FFFF}]+/u', '', $string);
        if ($removeDigits) {
            $string = preg_replace('/[0-9]+/', '', $string);
        }
        return $string;
    }
}
