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
        'authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJhcGktc2hpcHgtcGwuZWFzeXBhY2syNC5uZXQiLCJzdWIiOiJhcGktc2hpcHgtcGwuZWFzeXBhY2syNC5uZXQiLCJleHAiOjE1MzU3MDIyODEsImlhdCI6MTUzNTcwMjI4MSwianRpIjoiODBlYmJiZmYtMjY4Yy00ZGU3LWJiM2YtYWM5M2Y0N2ZmOGYzIn0._VCy_poxPjgq9tlF3h2IwyAz3eGE4ougapVaBG7zuXQ32SCzEHO2LfdkWbBGy5PUavdTTYUlrH3hyp9m5pcCJA',
        'tracking_url' => 'https://api-shipx-pl.easypack24.net/v1/organizations/3336/shipments?tracking_number='
    ],
    'dpd' => [
        'fid' => '239325',
        'username' => '23932501',
        'password' => 'YHskMhxk3f8DiHtF',
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
