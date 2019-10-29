<?php
/**
 * Author: Sebastian Rogala
 * Mail: sebrogala@gmail.com
 * Created: 07.01.2019
 */

include dirname(__FILE__).'./../vendor/autoload.php';

$baseUrl = env("APP_URL", "http://mega1000.loc");

$client = new \GuzzleHttp\Client(['header' => ['Content-Type' => 'application/json']]);

$response = $client->post($baseUrl . '/oauth/token', [
    'form_params' => [
        'client_id' => 2,
        'client_secret' => 'Cgm7yxvRbzR8ZEFXWBt1uTt5ySnLJfB31y6MZQjg',
        "grant_type" => "client_credentials"
    ]
]);

$auth = json_decode((string)$response->getBody());

$response = $client->post($baseUrl . '/api/customers', [
    'headers' => [
        'Authorization' => $auth->token_type . ' ' . $auth->access_token,
        'X-Requested-With' => 'XMLHttpRequest',
    ],
    'form_params' => [
        'login' => 'JosueQuitzon',
        'password' => '$2y$12$z5/CDNHZP.h4L8LjKm83Z.xImAq7vGyRNJfCkkmJhiDFxSNNtgGWW',
        'standard_address' => [
            "firstname" => "Holden",
            "lastname" => "Goldner",
            "firmname" => "Company S.A.",
            "phone" => 676321333,
            "email" => "nader.adella@hotmail.com",
            "city" => "Kleinland",
            "address" => "990 Elsa Island Apt. 803",
            "postal_code" => "52404-7350",
        ]
    ],
]);

$statusCode = $response->getStatusCode();

if($statusCode === 201) {
    dd("201 Created");
}

$response = $response->getBody()->getContents();

dd($response);
