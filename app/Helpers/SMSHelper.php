
<?php

namespace App\Helpers;

class SMSHelper
{
    public static function sendSms($msisdn, $from, $message, $bulkVariant = "PRO")
    {
        $appKey = "JDJhJDEyJFJ0aUM3b25LSmpHY0VhejE1cWpRYmU0VTVlMk8yU3Joa1lOWm5tQ2VGdnR4YkttUG1yUTVP";

        $sendData = [
            'to' => $msisdn,
            'from' => $from,
            'message' => $message,
            'bulkVariant' => $bulkVariant,
            'doubleEncode' => false
        ];

        $headers = array(
            'Content-Type: application/json',
            'App-Key: ' . $appKey
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://justsend.pl/api/rest/v2/message/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sendData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }
}
