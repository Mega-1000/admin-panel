<?php

return [
    'host'       => env('MAIL_NOTIFICATION_HOST', 's104.linuxpl.com'),
    'username'   => env('MAIL_NOTIFICATION_USERNAME', 'awizacje@ephpolska.pl'),
    'password'   => env('MAIL_NOTIFICATION_PASSWORD', '1!QQaazz2@WWssxx'),
    'port'       => env('MAIL_NOTIFICATION_PORT', 587),
    'encryption' => env('MAIL_NOTIFICATION_ENCRYPTION', 'tls'),
    'from'       => env('MAIL_NOTIFICATION_FROM', 'awizacje@ephpolska.pl'),
];