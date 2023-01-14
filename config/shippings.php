<?php

return [

    'providers' => [
        'schenker' => [
            'client_id' => env('SCHENKER_CLIENT_ID'),
            'user_name' => env('SCHENKER_USER_NAME'),
            'user_password' => env('SCHENKER_USER_PASSWORD'),
            'default_date_time_format' => env('SCHENKER_DATES_FORMAT', 'Y-m-dTH:i:s'),
        ]
    ]

];
