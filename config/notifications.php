<?php

return [
    'host'       => env('MAIL_NOTIFICATION_HOST', 's84.cyber-folks.pl'),
    'username'   => env('MAIL_NOTIFICATION_USERNAME', 'info@ephpolska.pl'),
    'password'   => env('MAIL_NOTIFICATION_PASSWORD', '1!Qaaa2@Wsss'),
    'port'       => env('MAIL_NOTIFICATION_PORT', 587),
    'encryption' => env('MAIL_NOTIFICATION_ENCRYPTION', 'tls'),
    'from'       => env('MAIL_NOTIFICATION_FROM', 'info@ephpolska.pl'),
];
