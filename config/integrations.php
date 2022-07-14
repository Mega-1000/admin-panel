<?php

return [
    'apaczka' => [
        'login' => 'info@mega1000.pl',
        'password' => env('APACZKA_PASSWORD'),
        'apiKey' => env('APACZKA_API_KEY'),
        'tracking_url' => 'www.apaczka.pl/check_status.php?waybill_number=',
        'appId' => env('APACZKA_APP_ID'),
        'appSecret' => env('APACZKA_APP_SECRET')
    ],
    'inpost' => [
        'url' => env('INPOST_URL'),
        'authorization' => env('INPOST_AUTHORIZATION_KEY'),
        'tracking_url' => env('INPOST_URL').'/v1/organizations/'.env('INPOST_ORG_ID').'/shipments?tracking_number=',
        'org_id' => env('INPOST_ORG_ID'),
    ],
    'dpd' => [
        'fid' => '338556',
        'username' => '33855601',
        'password' => '7NxOOlh5LdNL6mez',
        'wsdl' => 'https://dpdservices.dpd.com.pl/DPDPackageObjServicesService/DPDPackageObjServices?WSDL',
        'lang_code' => 'PL',
        'api_version' => 4,
        'debug' => false,
        'log_errors' => false,
        'log_path' => 'logs',
        'timezone' => 'Europe/Warsaw',
        'tracking_url' => 'https://tracktrace.dpd.com.pl/findPackage'
    ],
    'pocztex' => [
        'login' => env('POCZTEX_LOGIN'),
        'password' => env('POCZTEX_PASSWORD'),
        'trace' => env('POCZTEX_TRACE'),
        'tracking_url' => env('POCZTEX_URL')
    ],
    'jas' => [
        'testing_url' => 'http://webservice2.jasfbg.com.pl/Service.asmx?WSDL',
        'production_url' => 'https://service10.jasfbg.com.pl/Service.asmx?WSDL',
        'testing_login' => 'mega1000bis',
        'testing_password' => '[p/#Zz5F',
        'production_login' => 'ebudownictwo@wp.pl',
        'production_password' => 'MEGA1000'
    ],
    'gls' => [
        'tracking_url' => 'https://gls-group.eu/app/service/open/rest/PL/pl/rstt001?match=',
    ],
];
