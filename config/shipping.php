<?php

return [

    'sender' => [
        'name' => env('SENDER_NAME'),
        'tax_number' => env('SENDER_TAX_NUMBER'),
        'post_code' => env('SENDER_POST_CODE'),
        'city' => env('SENDER_CITY'),
        'street' => env('SENDER_STREET'),
        'house_number' => env('SENDER_HOUSE_NUMBER'),
        'local_number' => env('SENDER_LOCAL_NUMBER'),
        'email' => env('SENDER_EMAIL'),
        'phone' => env('SENDER_PHONE'),
    ],

    'payer' => [
        'name' => env('PAYER_NAME'),
        'tax_number' => env('PAYER_TAX_NUMBER'),
        'post_code' => env('PAYER_POST_CODE'),
        'city' => env('PAYER_CITY'),
        'street' => env('PAYER_STREET'),
        'house_number' => env('PAYER_HOUSE_NUMBER'),
        'local_number' => env('PAYER_LOCAL_NUMBER'),
        'email' => env('PAYER_EMAIL'),
        'phone' => env('PAYER_PHONE'),
    ]
];
