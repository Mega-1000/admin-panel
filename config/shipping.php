<?php

return [

    'sender' => [
        'name' => env('SENDER_NAME', 'ELEKTRONICZNA PLATFORMA HANDLOWA PL Sp. z o.o.'),
        'contact_person' => env('SENDER_CONTACT_PERSON', 'Wojciech Weissbrot'),
        'tax_number' => env('SENDER_TAX_NUMBER', '8993023676'),
        'post_code' => env('SENDER_POST_CODE', '53-022'),
        'city' => env('SENDER_CITY', 'Wrocław'),
        'street' => env('SENDER_STREET', 'UL. ks.Piotra Wawrzyniaka'),
        'house_number' => env('SENDER_HOUSE_NUMBER', '41'),
        'local_number' => env('SENDER_LOCAL_NUMBER', ''),
        'email' => env('SENDER_EMAIL', 'logistyka@ephpolska.pl'),
        'phone' => env('SENDER_PHONE', '691801594'),
    ],

    'payer' => [
        'name' => env('PAYER_NAME', 'ELEKTRONICZNA PLATFORMA HANDLOWA PL Sp. z o.o.'),
        'contact_person' => env('SENDER_CONTACT_PERSON', 'Wojciech Weissbrot'),
        'tax_number' => env('PAYER_TAX_NUMBER', '8993023676'),
        'post_code' => env('PAYER_POST_CODE', '53-022'),
        'city' => env('PAYER_CITY', 'Wrocław'),
        'street' => env('PAYER_STREET', 'UL. ks.Piotra Wawrzyniaka'),
        'house_number' => env('PAYER_HOUSE_NUMBER', '41'),
        'local_number' => env('PAYER_LOCAL_NUMBER', ''),
        'email' => env('PAYER_EMAIL', 'logistyka@ephpolska.pl'),
        'phone' => env('PAYER_PHONE', '691801594'),
    ],

    'package_divide_margin' => env('PACKAGE_DIVIDE_MARGIN', ''),
];
