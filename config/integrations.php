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
        'url' => 'https://api-shipx-pl.easypack24.net',
        'authorization' => env('INPOST_AUTHORIZATION_KEY'),
        'tracking_url' => 'https://api-shipx-pl.easypack24.net/v1/organizations/'.env('INPOST_ORG_ID').'/shipments?tracking_number=',
        'org_id' => env('INPOST_ORG_ID')
    ],
    'dpd' => [
        'fid' => '279242',
        'username' => '27924201',
        'password' => 'yDdRigACZCxR7TVA',
        'wsdl' => 'https://dpdservices.dpd.com.pl/DPDPackageObjServicesService/DPDPackageObjServices?WSDL',
        'lang_code' => 'PL',
        'api_version' => 4,
        'debug' => false,
        'log_errors' => false,
        'log_path' => 'logs',
        'timezone' => 'Europe/Warsaw',
        'tracking_url' => 'https://www.kurierem.pl/sledzenie-paczek-DPD'
    ],
    'pocztex' => [
        'tracking_url' => 'http://mobilna.poczta-polska.pl/MobiPost/getpackage?action=getPackageData&search='
    ],
    'jas' => [
        'testing_url' => 'http://webservice2.jasfbg.com.pl/Service.asmx?WSDL',
        'production_url' => 'https://service10.jasfbg.com.pl/Service.asmx?WSDL',
        'testing_login' => 'mega1000bis',
        'testing_password' => '[p/#Zz5F',
        'production_login' => 'ebudownictwo@wp.pl',
        'production_password' => 'MEGA1000'
    ],

];
