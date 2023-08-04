<?php

return [
    'client_id' => env('ALLEGRO_CLIENT_ID'),
    'client_secret' => env('ALLEGRO_CLIENT_SECRET'),
	'sandbox' => env('ALLEGRO_SANDBOX'),
    'payInPath' => env('ALLEGRO_PAY_IN_PATH', '/allegro-pay-in/'),
    'payInMailReceiver' => env('ALLEGRO_PAY_IN_MAIL_RECEIVER', 'ksiegowosc@ephpolska.pl'),
];
