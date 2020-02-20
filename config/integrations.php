<?php

return [
    'apaczka' => [
        'login' => 'ebudownictwo@wp.pl',
        'password' => 'aaaaaaaaaa',
        'apiKey' => 'abcdefg111',
        'tracking_url' => 'www.apaczka.pl/check_status.php?waybill_number='
    ],
    'inpost' => [
        'url' => 'https://api-shipx-pl.easypack24.net',
        'authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJhcGktc2hpcHgtcGwuZWFzeXBhY2syNC5uZXQiLCJzdWIiOiJhcGktc2hpcHgtcGwuZWFzeXBhY2syNC5uZXQiLCJleHAiOjE1ODE2NzczNTYsImlhdCI6MTU4MTY3NzM1NiwianRpIjoiOTZlNzY4OGEtMzI4YS00ZWU2LWE4MzMtOGUzZGMyZGZjNzQ5In0.F3Ai66dzY-VAiHl8W3XuIrr8w-yachNo9IxjOsg41RpmFcmNNpGLNQ46ji1JGkpbV7Jx0g2R1Nn0hjfQzpCzBQ. ',
        'tracking_url' => 'https://api-shipx-pl.easypack24.net/v1/organizations/14565/shipments?tracking_number='
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
